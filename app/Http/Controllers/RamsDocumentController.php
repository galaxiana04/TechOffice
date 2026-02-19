<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Cache;

use App\Models\ProjectType;
use App\Models\Wagroupnumber;
use App\Models\CollectFile;
use App\Models\RamsDocument;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Models\RamsDocumentFeedback;
use App\Models\Notification;
use App\Http\Controllers\LogController;
use Illuminate\Support\Facades\DB;
use App\Models\Unit;
use Illuminate\Support\Facades\Log;
use SebastianBergmann\CodeCoverage\Report\Xml\Project;
use App\Services\TelegramService;

class RamsDocumentController extends Controller
{
    protected $logController;
    protected $bottelegramController;

    public function __construct(LogController $logController, BotTelegramController $bottelegramController)
    {
        $this->logController = $logController;
        $this->bottelegramController = $bottelegramController;
    }




    public function create()
    {
        $listpic = ['Product Engineering', 'Mechanical Engineering System', 'Electrical Engineering System', 'Quality Engineering', 'Desain Mekanik & Interior', 'Desain Bogie & Wagon', 'Desain Carbody', 'Desain Elektrik', 'Preparation & Support', 'Welding Technology', 'Shop Drawing', 'Teknologi Proses'];
        $listproject = Cache::remember('list_project_titles', 180, function () {
            return ProjectType::pluck('title')->all();
        });

        return view('rams.create', compact('listproject', 'listpic'));
    }

    public function storeDocument(Request $request)
    {
        // Validasi input
        $request->validate([
            'documentname' => 'required|string|max:255',
            'documentnumber' => 'required|string|max:255',
            'proyek_type' => 'required|string|max:255',
            'ramsdocument_unit' => 'required|array',
            'filenames.*' => 'required|file|mimes:pdf,jpg,jpeg,png,doc,docx,xls,xlsx',
        ]);

        DB::beginTransaction();
        try {
            $userName = auth()->user()->name;

            // Simpan data RamsDocument
            $projecttype = ProjectType::where('title', $request->proyek_type)->firstOrFail(); // Validasi proyek_type

            $ramsdocument = RamsDocument::create([
                'documentname' => $request->documentname,
                'documentnumber' => $request->documentnumber,
                'proyek_type' => $request->proyek_type,
                'ramsdocument_unit' => json_encode($request->ramsdocument_unit),
                'project_type_id' => $projecttype->id,
            ]);

            $files = [];

            // Proses upload file
            if ($request->hasFile('filenames')) {






                foreach ($request->file('filenames') as $uploadedFile) {
                    // Dapatkan nama file asli dan formatnya
                    $filename = $uploadedFile->getClientOriginalName();
                    $fileFormat = $uploadedFile->getClientOriginalExtension();
                    $filenameWithoutExtension = pathinfo($filename, PATHINFO_FILENAME);

                    // Gabungkan nama file, nama pengguna, dan format file
                    $newFilename = "{$filenameWithoutExtension}_{$userName}.{$fileFormat}";

                    // Periksa apakah nama file sudah ada, dan buat nama baru jika perlu
                    $count = 0;
                    while (CollectFile::where('filename', $newFilename)->exists()) {
                        $count++;
                        $newFilename = "{$filenameWithoutExtension}_{$count}.{$fileFormat}";
                    }

                    // Simpan file di folder 'public/uploads'
                    $path = $uploadedFile->storeAs('public/uploads', $newFilename);

                    // Simpan informasi file ke database
                    $newmemoFile = new CollectFile();
                    $newmemoFile->filename = $newFilename;
                    $newmemoFile->link = str_replace('public/', '', $path); // Hapus 'public/' dari path
                    $newmemoFile->collectable_id = $ramsdocument->id;
                    $newmemoFile->collectable_type = RamsDocument::class;



                    $newmemoFile->save();

                    $files[] = $newmemoFile;
                }
            }

            $notifSystemData = [];
            // Kirim pesan ke setiap unit
            if (!empty($request->ramsdocument_unit)) {
                foreach ($request->ramsdocument_unit as $unit) {
                    try {
                        $list = '';

                        // Daftar file untuk pesan
                        foreach ($files as $file) {
                            $list .= "📄 *{$file->filename}* ➡️ 🔗 Downloadfile_{$file->id}\n";
                        }

                        // Pesan yang dikirimkan
                        $message = "RAMS dokumen {$request->documentname} dikirimkan ke {$unit} untuk dikerjakan.\n\n" .
                            "📂 Link : http://192.168.13.160:8000/rams/" . $ramsdocument->id .
                            "📂 Berikut daftar dokumen yang bisa diunduh melalui WhatsApp:\n{$list}\n" .
                            "🚀 *Ayo segera dikerjakan!* Terima kasih atas kerjasamanya! 🙏😊";

                        TelegramService::ujisendunit($unit, $message);
                        $notifSystemData[] = [
                            'notifmessage_id' => $ramsdocument->id,
                            'notifmessage_type' => RamsDocument::class,
                            'status' => 'unread',
                            'idunit' => $unit->id,
                            'infostatus' => 'User dont read this message',
                            'notifarray' => json_encode(['type' => 'order', 'message' => 'Order received']),
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    } catch (\Exception $e) {
                        Log::error('Error sending message: ' . $e->getMessage());
                    }
                }
            }


            Notification::insert($notifSystemData);

            DB::commit();
            // Redirect dengan pesan sukses
            return redirect()->route('ramsdocuments.show', $ramsdocument)
                ->with('success', 'Document and files created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error in storing data: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to create document. ' . $e->getMessage());
        }
    }


    public function indexterbuka()
    {
        $documents = RamsDocument::infoplus($status = "Terbuka");
        $listproject = Cache::remember('list_project_titles', 180, function () {
            return ProjectType::pluck('title')->all();
        });

        for ($i = 0; $i < count($listproject); $i++) {
            $key = str_replace(' ', '_', $listproject[$i]);
            $filteredDocuments = collect($documents)->where('proyek_type', $listproject[$i])->all();
            // Simpan dokumen yang telah difilter ke dalam revisiall
            $revisiall[$key]['documents'] = $filteredDocuments;
        }

        return view('rams.index', compact('documents', 'revisiall'));
    }

    public function indextertutup()
    {
        $documents = RamsDocument::infoplus($status = "Tertutup");
        $listproject = Cache::remember('list_project_titles', 180, function () {
            return ProjectType::pluck('title')->all();
        });

        for ($i = 0; $i < count($listproject); $i++) {
            $key = str_replace(' ', '_', $listproject[$i]);
            $filteredDocuments = collect($documents)->where('proyek_type', $listproject[$i])->all();
            // Simpan dokumen yang telah difilter ke dalam revisiall
            $revisiall[$key]['documents'] = $filteredDocuments;
        }

        return view('rams.index', compact('documents', 'revisiall'));
    }

    public function show($id)
    {
        $auth = auth()->user();
        $nama_divisi = $auth->rule;
        // Retrieve the RamsDocument instance and eager load the related models
        $document = RamsDocument::with('files', 'feedbacks.feedbackfiles')->findOrFail($id);

        $ramsUnit = json_decode($document->ramsdocument_unit);
        $files = $document->files;
        $feedbacks = $document->feedbacks;
        $data = $document->getVerificatorData();
        $unitpicvalidation = $data['unitpicvalidation'];
        $unitvalidation = $data['unitvalidation'];
        $ramscombinevalidation = $data['ramscombinevalidation'];
        $smunitpicvalidation = $data['smunitpicvalidation'];
        $smunitvalidation = $data['smunitvalidation'];
        $ramsfinalisasivalidation = $data['ramsfinalisasivalidation'];
        $ramscombinesendvalidation = $data['ramscombinesendvalidation'];

        $unit = Unit::where('name', $nama_divisi)->first();
        // Cari tugas divisi yang pertama ditemukan
        $tugasdivisis = Notification::where('notifmessage_id', $document->id)
            ->where('idunit', $unit->id)
            ->where("notifmessage_type", "App\\Models\\MemoSekdiv")
            ->get();

        // Jika tugas divisi ditemukan, update status sudah dibaca
        if ($tugasdivisis->isNotEmpty()) {
            foreach ($tugasdivisis as $tugasdivisi) {
                $tugasdivisi->update([
                    'status' => "read",
                ]);
            }
        }

        return view('rams.show', compact('document', 'ramsUnit', 'files', 'feedbacks', 'unitpicvalidation', 'unitvalidation', 'ramscombinevalidation', 'ramscombinesendvalidation', 'smunitpicvalidation', 'smunitvalidation', 'ramsfinalisasivalidation'));
    }

    public function viewfeedback($id, $level)
    {
        $ramsdocument = RamsDocument::findOrFail($id);
        $kind = 'feedback';
        return view('rams.feedback', compact('ramsdocument', 'kind', 'level'));
    }

    public function viewfinalisasi($id, $level)
    {
        $ramsdocument = RamsDocument::findOrFail($id);
        $kind = 'finalisasi';
        return view('rams.feedback', compact('ramsdocument', 'kind', 'level'));
    }

    public function viewsmfeedback($id, $level)
    {
        $ramsdocument = RamsDocument::findOrFail($id);
        $kind = 'smfeedback';
        return view('rams.feedback', compact('ramsdocument', 'kind', 'level'));
    }

    public function viewcombine($id, $level)
    {
        $ramsdocument = RamsDocument::findOrFail($id);
        $kind = 'combine';
        return view('rams.feedback', compact('ramsdocument', 'kind', 'level'));
    }

    public function submitFeedbackCombine(Request $request, $id)
    {

        DB::beginTransaction();
        try {
            $rams_document = RamsDocument::findOrFail($id);
            if ($request->kind == 'finalisasi') {
                $rams_document->status = 'Tertutup';
                $rams_document->save();
            }
            $user = auth()->user();
            $userName = $user->name;

            $pesan = "📢 *Feedback Baru pada RAMS Document!* 📢\n"
                . "📄 *Nama Dokumen:* {$rams_document->documentname}\n"
                . "📄 *No Dokumen:* {$rams_document->documentnumber}\n"
                . "🗣️ Feedback diberikan oleh " . $userName . "*\n\n"
                . "📅 Tinjau segera untuk kelancaran proses! 🚀";

            // Kirim pesan WA ke unit terkait
            TelegramService::ujisendunit("RAMS", $pesan);



            // Buat entri umpan balik
            $feedback = new RamsDocumentFeedback();
            $feedback->rams_document_id = $rams_document->id;
            $feedback->pic = $request->pic;
            $feedback->author = $request->author;
            $feedback->level = $request->level ?? "";
            $feedback->email = $request->email;
            $feedback->comment = $request->comment ?? "";
            $feedback->conditionoffile = $request->conditionoffile;
            $feedback->conditionoffile2 = $request->conditionoffile2;
            $feedback->save();











            // Tangani unggahan file multiple
            if ($request->hasFile('filenames')) {
                foreach ($request->file('filenames') as $uploadedFile) {
                    // Dapatkan nama file asli dan formatnya
                    $filename = $uploadedFile->getClientOriginalName();
                    $fileFormat = $uploadedFile->getClientOriginalExtension();
                    $filenameWithoutExtension = pathinfo($filename, PATHINFO_FILENAME);

                    // Gabungkan nama file, nama pengguna, dan format file
                    $newFilename = "{$filenameWithoutExtension}_{$userName}.{$fileFormat}";

                    // Periksa apakah nama file sudah ada, dan buat nama baru jika perlu
                    $count = 0;
                    while (CollectFile::where('filename', $newFilename)->exists()) {
                        $count++;
                        $newFilename = "{$filenameWithoutExtension}_{$count}.{$fileFormat}";
                    }

                    // Simpan file di folder 'public/uploads'
                    $path = $uploadedFile->storeAs('public/uploads', $newFilename);

                    // Simpan informasi file ke database
                    $newmemoFile = new CollectFile();
                    $newmemoFile->filename = $newFilename;
                    $newmemoFile->link = str_replace('public/', '', $path); // Hapus 'public/' dari path
                    $newmemoFile->collectable_id = $feedback->id;
                    $newmemoFile->collectable_type = RamsDocumentFeedback::class;
                    $newmemoFile->save();
                }
            }






            DB::commit();
            return redirect()->route('ramsdocuments.show', $rams_document->id)->with('success', 'Feedback submitted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error in storing data: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to submit feedback. ' . $e->getMessage());
        }
    }


    public function sendSM(Request $request, $id)
    {
        // Validasi input
        $request->validate([
            'sm_unit' => 'required|array',
            'sm_unit.*' => 'required|string'
        ]);

        DB::beginTransaction();
        try {
            // Temukan dokumen umpan balik berdasarkan ID
            $rams_feedbackdocument = RamsDocumentFeedback::with('document')->findOrFail($id);
            $ramsdocument = $rams_feedbackdocument->document;
            $allunitundersm = $request->sm_unit;

            $notifSystemData = [];
            $allidfile = [];
            foreach ($allunitundersm as $unit) {
                // Buat entri umpan balik baru
                $feedback = RamsDocumentFeedback::create([
                    'rams_document_id' => $rams_feedbackdocument->rams_document_id,
                    'pic' => $rams_feedbackdocument->pic,
                    'author' => $rams_feedbackdocument->author,
                    'level' => $unit,
                    'email' => $rams_feedbackdocument->email,
                    'comment' => $rams_feedbackdocument->comment,
                    'conditionoffile' => 'filesend',
                    'conditionoffile2' => 'smfeedback'
                ]);

                // Handle multiple file uploads
                if ($rams_feedbackdocument->feedbackfiles) {
                    foreach ($rams_feedbackdocument->feedbackfiles as $file) {
                        // Buat entri file umpan balik baru
                        $file = CollectFile::create([
                            'filename' => $file->filename,
                            'link' => $file->link,
                            'collectable_id' => $feedback->id,
                            'collectable_type' => RamsDocumentFeedback::class,
                        ]);
                        $idfile = $file->id;
                        $allidfile[] = $idfile;
                    }
                }

                $notifSystemData[] = [
                    'notifmessage_id' =>  $ramsdocument->id,
                    'notifmessage_type' => RamsDocument::class,
                    'status' => 'unread',
                    'idunit' => $unit->id,
                    'infostatus' => 'User dont read this message',
                    'notifarray' => json_encode(['type' => 'order', 'message' => 'Order received']),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }





            Notification::insert($notifSystemData);







            // Kirim notifikasi ke unit SM
            if (isset($request->sm_unit)) {
                foreach ($request->sm_unit as $pic) {
                    try {
                        $namaFile = $rams_feedbackdocument->documentname;

                        $list = '';

                        CollectFile::whereIn('id', $allidfile)->get()->each(function ($file) use (&$list) {
                            $list .= "📄 *{$file->filename}* ➡️ 🔗 Downloadfile_{$file->id}\n";
                        });
                        // Pesan yang dikirimkan
                        $message = "Dokumen Rams (Perlu Approve) {$namaFile} dikirimkan ke {$pic} untuk dikerjakan.\n\n" .
                            "📂 Link : http://192.168.13.160:8000/rams/" . $ramsdocument->id .
                            "📂 Berikut daftar dokumen yang bisa diunduh melalui WhatsApp:\n{$list}\n" .
                            "🚀 *Ayo segera dikerjakan!* Terima kasih atas kerjasamanya! 🙏😊";

                        TelegramService::ujisendunit($pic, $message);
                    } catch (\Exception $e) {
                        Log::error('Error in storing data: ' . $e->getMessage());
                    }
                }
            }
            DB::commit();
            return redirect()->route('ramsdocuments.show', $rams_feedbackdocument->rams_document_id)->with('success', 'Feedback submitted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error in storing data: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to send SM. ' . $e->getMessage());
        }
    }



    public function destroyFeedback($documentId, $feedbackId)
    {
        DB::beginTransaction();
        try {
            $hazardLog = RamsDocument::findOrFail($documentId);
            $hazardLogFeedback = RamsDocumentFeedback::findOrFail($feedbackId);

            // Delete associated files (if needed)
            $hazardLogFeedback->hazardLogFiles()->delete();

            // Delete the feedback
            $hazardLogFeedback->delete();
            DB::commit();
            return redirect()->route('rams.show', $documentId)->with('success', 'Feedback deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to delete feedback. ' . $e->getMessage());
        }
    }

    public function approveFeedback($documentId, $feedbackId)
    {
        DB::beginTransaction();
        try {
            $hazardLog = RamsDocument::findOrFail($documentId);
            $hazardLogFeedback = RamsDocumentFeedback::findOrFail($feedbackId);
            $hazardLogFeedback->conditionoffile = "approve";
            $hazardLogFeedback->update();
            // Optionally, you can also send a notification or perform other actions here
            DB::commit();
            return redirect()->route('rams.show', $documentId)->with('success', 'Feedback deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to delete feedback. ' . $e->getMessage());
        }
    }
    public function rejectFeedback($documentId, $feedbackId)
    {
        DB::beginTransaction();
        try {
            $hazardLog = RamsDocument::findOrFail($documentId);
            $hazardLogFeedback = RamsDocumentFeedback::findOrFail($feedbackId);
            $hazardLogFeedback->conditionoffile = "reject";
            $hazardLogFeedback->update();
            // Optionally, you can also send a notification or perform other actions here
            DB::commit();
            return redirect()->route('rams.show', $documentId)->with('success', 'Feedback deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to delete feedback. ' . $e->getMessage());
        }
    }

    public function update(Request $request, RamsDocument $document)
    {
        $request->validate([
            'documentname' => 'required|string|max:255',
            'documentnumber' => 'required|string|max:255',
        ]);
        DB::beginTransaction();
        try {

            $document->update($request->all());

            DB::commit();
            return redirect()->route('ramsdocuments.indexterbuka')
                ->with('success', 'Document updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to check permissions: ' . $e->getMessage());
        }
    }

    public function destroy(RamsDocument $document)
    {
        DB::beginTransaction();
        try {
            $document->delete();

            DB::commit();
            return redirect()->route('ramsdocuments.indexterbuka')
                ->with('success', 'Document deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to delete document: ' . $e->getMessage());
        }
    }
}
