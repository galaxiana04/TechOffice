<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Models\Newbomkomat;
use App\Models\KomatHistReq;
use App\Models\KomatProcessHistory;
use App\Models\KomatProcess;
use App\Models\KomatProcessHistoryTimeline;
use App\Models\KomatPosition;
use App\Models\KomatFeedback;
use App\Models\Unit;
use App\Models\ProjectType;
use App\Models\KomatRequirement;
use App\Models\CollectFile;
use App\Services\TelegramService;
use App\Models\KomatSupplier;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class KomatProcessController extends Controller
{


    // Upload dokumen logistik
    public function uploadDocLogistik(Request $request)
    {

        // Validasi request
        $request->validate([
            'file.*' => 'required',
            'proyek_type_id' => 'required',
        ]);

        $user = auth()->user();
        $userName = $user->name;

        DB::beginTransaction();
        try {
            $project_type_ids = $request->input('proyek_type_id'); // Array of project type IDs
            $kodematerial_name = strtoupper($request->input('kodematerial'));
            $komat_supplier_id = $request->input('komat_supplier_id');
            $requirement_id_list = $request->input('requirement_list_id');
            $note = $request->input('note');
            foreach ($project_type_ids as $project_type_id) {
                foreach ($requirement_id_list as $requirement_id) {


                    // Query komat based on single project_type_id
                    $komat = Newbomkomat::with('newbom')
                        ->where('kodematerial', $kodematerial_name)
                        ->whereHas('newbom', function ($query) use ($project_type_id) {
                            $query->where('proyek_type_id', $project_type_id);
                        })->first();

                    $unituploader = Unit::where('name', 'Logistik')->first();
                    if (!$komat) {
                        $komat_id = null;
                        $unit = Unit::where('name', 'Product Engineering')->first();
                    } else {
                        $komat_id = $komat->id;
                        $unit = Unit::where('name', $komat->newbom->unit)->first();
                    }

                    $komatprocess = KomatProcess::firstOrCreate([
                        'komat_name' => $kodematerial_name,
                        'komat_id' => $komat_id,
                    ]);

                    $revision = '0'; // Start with '0'
                    if (!$komatprocess->wasRecentlyCreated) {
                        $lastHistory = $komatprocess->komatProcessHistories()
                            ->where('komat_supplier_id', $komat_supplier_id)->where('project_type_id', $project_type_id)->where('komat_requirement_id', $requirement_id)
                            ->latest()
                            ->first();

                        if ($lastHistory) {
                            $currentRevision = $lastHistory->revision;

                            // If the current revision is '0', start with 'A'
                            if ($currentRevision === '0') {
                                $revision = 'A';
                            } else {
                                // Increment the revision based on ASCII value
                                $asciiValue = ord($currentRevision);
                                $revision = chr($asciiValue + 1);
                            }
                        }
                    }

                    $authority_level = $request->input('authority_level');

                    $komatProcessHistory = KomatProcessHistory::create([
                        'komat_process_id' => $komatprocess->id,
                        'discussion_number' => 1,
                        'komat_supplier_id' => $komat_supplier_id,
                        'unit_distributor_id' => $unit->id,
                        'documentstatus' => "ongoing",
                        'logisticauthoritylevel' => $authority_level,
                        'status' => "Terbuka",
                        'revision' => $revision,
                        'note' => $note,
                        'project_type_id' => $project_type_id, // Store single project_type_id
                    ]);

                    $listcollectable_id = [];

                    $komatHistReq = KomatHistReq::firstOrCreate([
                        'komat_process_history_id' => $komatProcessHistory->id,
                        'komat_requirement_id' => $requirement_id,
                    ]);

                    $komatPosition = KomatPosition::create([
                        'komat_hist_req_id' => $komatHistReq->id,
                        'unit_id' => $unituploader->id,
                        'level' => "logistik_upload",
                        'status' => "approved",
                        'status_process' => "done",
                    ]);

                    $komatfeedback = KomatFeedback::create([
                        'komat_position_id' => $komatPosition->id,
                        'komat_process_history_id' => $komatProcessHistory->id,
                        'komat_requirement_id' => $requirement_id,
                        'comment' => "",
                        'status' => "last_accepted",
                        'user_id' => $user->id,
                        'user_rule' => $user->rule,
                        'user_name' => $user->name,
                    ]);

                    foreach ($request->file('file_' . $requirement_id) as $key => $uploadedFile) {
                        // Dapatkan nama file yang diunggah
                        $filename = $uploadedFile->getClientOriginalName();

                        // Dapatkan ekstensi file
                        $fileFormat = $uploadedFile->getClientOriginalExtension();

                        // Hapus ekstensi file dari nama file
                        $filenameWithoutExtension = pathinfo($filename, PATHINFO_FILENAME);

                        // Gabungkan nama file (tanpa ekstensi), nama pengguna, dan format file
                        $filenameWithUserAndFormat = $filenameWithoutExtension . '_' . $userName . '.' . $fileFormat;

                        // Sekarang, $filenameWithUserAndFormat berisi nama file yang dihasilkan dengan nama pengguna dan format file
                        $filename = $filenameWithUserAndFormat;

                        // Periksa apakah nama file sudah ada
                        $count = 0;
                        $newFilename = $filename;
                        while (CollectFile::where('filename', $newFilename)->exists()) {
                            $count++;
                            $newFilename = pathinfo($filename, PATHINFO_FILENAME) . '_' . $count . '.' . pathinfo($filename, PATHINFO_EXTENSION);
                        }

                        // Simpan file di storage/app/public/uploads
                        $path = $uploadedFile->storeAs('public/uploads', $newFilename);

                        // Simpan file terkait di database
                        $FeedbackFile = new CollectFile();
                        $FeedbackFile->filename = $newFilename;
                        $FeedbackFile->link = str_replace('public/', '', $path);
                        $FeedbackFile->collectable_id = $komatfeedback->id; // Menghubungkan file dengan feedback
                        $FeedbackFile->collectable_type = KomatFeedback::class; // Tipe polimorfik
                        $FeedbackFile->save();
                        $listcollectable_id[] = $komatfeedback->id;
                    }

                    $komatProcessHistory->notifsystem()->create([
                        'status' => 'unread',
                        'idunit' => $unit->id,
                        'infostatus' => 'User dont read this message',
                        'notifarray' => json_encode(['type' => 'order', 'message' => 'Order received']),
                    ]);

                    // Menyusun daftar file untuk di-download
                    $files = CollectFile::whereIn('collectable_id', $listcollectable_id)->where('collectable_type', KomatFeedback::class)->get();
                    $list = '';
                    foreach ($files as $file) {
                        $list .= "📄 *" . $file->filename . "* ➡️ 🔗 Downloadfile_" . $file->id . "\n";
                    }

                    $pesan = 'Komat Process: ' . $kodematerial_name . ' Rev: ' . $revision . ' telah dibuka dan ditunjukan ke unit :' . $unit->name . "\n\n" .
                        "📂 Berikut daftar dokumen yang bisa diunduh by WhatsApp:\n" .
                        $list .
                        "\n🚀 *Ayo segera dikerjakan!* Terima kasih atas kerjasamanya! 🙏😊";

                    // wa message
                    TelegramService::ujisendunit($unit->name, $pesan);

                    $komatProcessHistory->systemLogs()->create([
                        'message' => json_encode([
                            'message' => 'KomatProcessHistory berhasil dibuat',
                            'datasebelum' => '',
                            'datasesudah' => $komatProcessHistory,
                        ]),
                        'level' => 'info',
                        'user' => auth()->user()->name,
                        'user_id' => auth()->user()->id,
                        'aksi' => 'documentaddition',
                    ]);

                    KomatProcessHistoryTimeline::create([
                        'komat_process_history_id' => $komatProcessHistory->id,
                        'infostatus' => 'logisticopened',
                        'entertime' => now(),
                    ]);
                }
            }



            DB::commit();
            return redirect()->route('komatprocesshistory.show', $komatProcessHistory->id)
                ->with('success', 'Dokumen berhasil diunggah.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Dokumen gagal diunggah: ' . $e->getMessage());
        }
    }
    public function referencerelation(Request $request)
    {
        $validated = $request->validate([
            'kodematerial' => 'required|exists:newbomkomats,kodematerial',
        ]);

        // Ambil semua newbomkomat dengan kode material tsb
        $newbomkomats = Newbomkomat::where('kodematerial', $validated['kodematerial'])->get();

        if ($newbomkomats->isNotEmpty()) {
            // Kumpulkan semua requirement dari setiap newbomkomat
            $requirementNames = $newbomkomats->flatMap(function ($item) {
                return $item->requirements()->pluck('name');
            })->unique()->values(); // unique biar tidak dobel

            return response()->json([
                'success' => true,
                'data' => $requirementNames,
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan',
            ]);
        }
    }


    // Tampilkan formulir unggah dokumen
    public function showUploadForm()
    {
        $komats = Newbomkomat::with('newbom')->get();
        $listproject = ProjectType::all();
        $requirements = KomatRequirement::all();
        $komatSupplier = KomatSupplier::all();
        return view('komatprocesshistory.upload-doc', compact('listproject', 'requirements', 'komatSupplier', 'komats'));
    }

    public function addSupplier(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        try {
            $supplier = KomatSupplier::create([
                'name' => $request->name,
                'description' => $request->description,
            ]);

            return response()->json([
                'success' => true,
                'supplier' => $supplier,
                'message' => 'Supplier berhasil ditambahkan.'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Gagal menambahkan supplier: ' . $e->getMessage()
            ], 500);
        }
    }
    public function searchSuppliers(Request $request)
    {
        $query = $request->input('q', '');
        $page = $request->input('page', 1);
        $perPage = 10;

        $suppliers = KomatSupplier::where('name', 'like', '%' . $query . '%')
            ->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'suppliers' => $suppliers->items(),
            'pagination' => [
                'more' => $suppliers->hasMorePages()
            ]
        ]);
    }

    // Tampilkan dokumen
    public function showDocument($id)
    {
        $document = KomatProcessHistory::with(['projectType', 'feedbacks.files', 'komatHistReqs.komatPositions', 'komatProcess', 'supplier'])->findOrFail($id);
        $yourauth = auth()->user();
        $logs = [];
        $units = Unit::where('name', 'NOT LIKE', '%Manager%')
            ->where('is_technology_division', 1)
            ->get();

        return view('komatprocesshistory.show.show', compact('document', 'yourauth', 'logs', 'units'));
    }

    public function close(Request $request, $id)
    {
        // Validasi request
        $request->validate([
            'status' => 'required|string|max:255',
            'documentstatus' => 'required|string|max:255',
            'needincreaserevision' => 'required|in:yes,no',
            'rejectedreason' => 'nullable|string|max:1000',
        ]);

        $document = KomatProcessHistory::findOrFail($id);
        $user = auth()->user();
        $userName = $user->name;

        DB::beginTransaction();
        try {
            $rejectedreason = $request->input('rejectedreason');
            // Update status dokumen saat ini
            $document->status = $request->input('status');
            $document->rejectedreason = $rejectedreason;
            $document->documentstatus = $request->input('documentstatus');
            $document->save();

            $needincreaserevision = $request->input('needincreaserevision');
            if ($needincreaserevision === 'yes') {
                // Logika untuk menangani kebutuhan diskusi baru
                $komatProcess = $document->komatProcess;
                $currentDiscussionNumber = $document->discussion_number;

                // Tentukan nomor diskusi baru
                $newDiscussionNumber = $currentDiscussionNumber + 1;

                // Buat KomatProcessHistory baru dengan discussion_number baru
                $newKomatProcessHistory = KomatProcessHistory::create([
                    'komat_process_id' => $document->komat_process_id,
                    'komat_supplier_id' => $document->komat_supplier_id,
                    'unit_distributor_id' => $document->unit_distributor_id,
                    'documentstatus' => 'ongoing',
                    'logisticauthoritylevel' => $document->logisticauthoritylevel,
                    'status' => 'Terbuka',
                    'revision' => $document->revision, // Tetap gunakan revision yang sama
                    'note' => "Diskusi baru dibuat. Berikut alasannya: $rejectedreason",
                    'project_type_id' => $document->project_type_id,
                    'discussion_number' => $newDiscussionNumber, // Gunakan discussion_number baru
                ]);

                // Salin KomatHistReqs dari dokumen sebelumnya
                foreach ($document->komatHistReqs as $oldHistReq) {
                    $newHistReq = KomatHistReq::create([
                        'komat_process_history_id' => $newKomatProcessHistory->id,
                        'komat_requirement_id' => $oldHistReq->komat_requirement_id,
                    ]);

                    // Salin KomatPositions untuk level logistik_upload dan discussion
                    $levelsToCopy = ['logistik_upload', 'discussion'];
                    $oldPositions = $oldHistReq->komatPositions->whereIn('level', $levelsToCopy);

                    foreach ($oldPositions as $oldPosition) {
                        $newPosition = KomatPosition::create([
                            'komat_hist_req_id' => $newHistReq->id,
                            'unit_id' => $oldPosition->unit_id,
                            'level' => $oldPosition->level,
                            'status' => $oldPosition->status,
                            'status_process' => $oldPosition->status_process,
                        ]);

                        // Salin KomatFeedback dan CollectFile untuk posisi yang disalin
                        $oldFeedbacks = $oldPosition->feedbacks->where('status', 'last_accepted');
                        foreach ($oldFeedbacks as $oldFeedback) {
                            $newFeedback = KomatFeedback::create([
                                'komat_position_id' => $newPosition->id,
                                'komat_process_history_id' => $newKomatProcessHistory->id,
                                'komat_requirement_id' => $oldFeedback->komat_requirement_id,
                                'comment' => $oldFeedback->comment,
                                'status' => 'last_accepted',
                                'feedback_status' => $oldFeedback->feedback_status,
                                'user_id' => $user->id,
                                'user_rule' => $user->rule,
                                'user_name' => $user->name,
                            ]);

                            // Salin file terkait
                            foreach ($oldFeedback->files as $file) {
                                CollectFile::create([
                                    'filename' => $file->filename,
                                    'link' => $file->link,
                                    'collectable_id' => $newFeedback->id,
                                    'collectable_type' => KomatFeedback::class,
                                ]);
                            }
                        }
                    }
                }

                // Buat notifikasi sistem untuk dokumen baru
                $newKomatProcessHistory->notifsystem()->create([
                    'status' => 'unread',
                    'idunit' => $document->unit_distributor_id,
                    'infostatus' => 'User dont read this message',
                    'notifarray' => json_encode(['type' => 'order', 'message' => 'New discussion created']),
                ]);

                // Kirim notifikasi WhatsApp
                $unit = Unit::findOrFail($document->unit_distributor_id);
                $pesan = 'Komat Process: ' . $komatProcess->komat_name . ' Rev: ' . $newDiscussionNumber . ' Discussion Number: ' . $newDiscussionNumber . ' telah dibuka kembali untuk diskusi baru dan ditunjukkan ke unit: ' . $unit->name . "\n\n" .
                    "🚀 *Ayo segera dikerjakan!* Terima kasih atas kerjasamanya! 🙏😊";
                TelegramService::ujisendunit($unit->name, $pesan);

                // Log aktivitas pembuatan diskusi baru
                $newKomatProcessHistory->systemLogs()->create([
                    'message' => json_encode([
                        'message' => 'Diskusi baru KomatProcessHistory berhasil dibuat',
                        'datasebelum' => $document->toArray(),
                        'datasesudah' => $newKomatProcessHistory->toArray(),
                    ]),
                    'level' => 'info',
                    'user' => $userName,
                    'user_id' => $user->id,
                    'aksi' => 'discussion_creation',
                ]);

                // Buat entri timeline untuk dokumen baru
                KomatProcessHistoryTimeline::create([
                    'komat_process_history_id' => $newKomatProcessHistory->id,
                    'infostatus' => 'logisticopened',
                    'entertime' => now(),
                ]);
            }

            DB::commit();
            return redirect()->route('komatprocesshistory.show', $document->id)
                ->with('success', 'Dokumen telah ditutup.' . ($needincreaserevision === 'yes' ? ' Diskusi baru telah dibuat.' : ''));
        } catch (\Exception $e) {
            DB::rollBack();
            return ['error' => 'Gagal menutup dokumen atau membuat diskusi baru: ' . $e->getMessage()];
        }
    }

    // Tampilkan formulir edit dokumen
    public function komatprocesshistoryedit($id)
    {
        $document = KomatProcessHistory::with([
            'komatHistReqs.komatRequirement',
            'komatHistReqs.komatPositions',
        ])->findOrFail($id);

        $units = Unit::where('name', 'NOT LIKE', '%Manager%')
            ->where('is_technology_division', 1)
            ->get();

        return view('komatprocesshistory.editdokumen', compact('document', 'units'));
    }

    public function updatePositions(Request $request, $id)
    {
        $document = KomatProcessHistory::with('komatHistReqs.komatPositions')->findOrFail($id);

        // Data checkbox dari form, struktur: positions[komatHistReqId] = [unit_id, unit_id, ...]
        $positionsInput = $request->input('positions', []);

        DB::beginTransaction();
        try {
            // Loop tiap KomatHistReq pada dokumen ini
            foreach ($document->komatHistReqs as $komatHistReq) {
                $selectedUnits = $positionsInput[$komatHistReq->id] ?? [];

                // Ambil posisi yg sudah ada untuk komatHistReq ini dengan level discussion
                $existingPositions = $komatHistReq->komatPositions->where('level', 'discussion');

                // Hapus posisi yg sudah tidak dipilih (unchecked)
                foreach ($existingPositions as $pos) {
                    if (!in_array($pos->unit_id, $selectedUnits)) {
                        $pos->delete();
                    }
                }

                // Tambah posisi baru yg dipilih tapi belum ada
                foreach ($selectedUnits as $unitId) {
                    if (!$existingPositions->where('unit_id', $unitId)->count()) {
                        KomatPosition::create([
                            'komat_hist_req_id' => $komatHistReq->id,
                            'unit_id' => $unitId,
                            'level' => 'discussion',
                            'status' => 'draft',
                            'status_process' => 'not_started',
                        ]);
                    }
                }
            }

            DB::commit();
            return redirect()->route('komatprocesshistory.show', $document->id)
                ->with('success', 'Posisi unit berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'Gagal memperbarui posisi unit: ' . $e->getMessage()]);
        }
    }

    public function addComment(Request $request, $id, $komatHistReqId, $unitId)
    {
        // Validasi input
        $request->validate([
            'comment' => 'required|string|max:1000',
            'feedback_status' => 'required|in:draft,approved,notapproved,withremarks',
            'files.*' => 'nullable|file', // Validasi file
        ]);

        $user = auth()->user();
        $userName = $user->name;
        $document = KomatProcessHistory::findOrFail($id);
        $komatHistReq = KomatHistReq::findOrFail($komatHistReqId);
        $unit = Unit::findOrFail($unitId);

        // Pastikan komatHistReq terkait dengan dokumen dan unit memiliki posisi discussion
        $komatPosition = KomatPosition::where('komat_hist_req_id', $komatHistReq->id)
            ->where('unit_id', $unit->id)
            ->where('level', 'discussion')
            ->firstOrFail();

        DB::beginTransaction();
        try {
            // Buat feedback baru untuk komentar
            $komatfeedback = KomatFeedback::create([
                'komat_position_id' => $komatPosition->id,
                'komat_process_history_id' => $document->id,
                'komat_requirement_id' => $komatHistReq->komat_requirement_id,
                'feedback_status' => $request->input('feedback_status'),
                'comment' => $request->input('comment'),
                'status' => 'draft', // Status awal untuk komentar
                'user_id' => $user->id,
                'user_rule' => $user->rule,
                'user_name' => $user->name,
            ]);
            foreach ($request->file('file', []) as $uploadedFile) {
                // Dapatkan nama file yang diunggah
                $filename = $uploadedFile->getClientOriginalName();

                // Dapatkan ekstensi file
                $fileFormat = $uploadedFile->getClientOriginalExtension();

                // Hapus ekstensi file dari nama file
                $filenameWithoutExtension = pathinfo($filename, PATHINFO_FILENAME);

                // Gabungkan nama file (tanpa ekstensi), nama pengguna, dan format file
                $filenameWithUserAndFormat = $filenameWithoutExtension . '_' . $userName . '.' . $fileFormat;

                // Sekarang, $filenameWithUserAndFormat berisi nama file yang dihasilkan dengan nama pengguna dan format file
                $filename = $filenameWithUserAndFormat;

                // Periksa apakah nama file sudah ada
                $count = 0;
                $newFilename = $filename;
                while (CollectFile::where('filename', $newFilename)->exists()) {
                    $count++;
                    $newFilename = pathinfo($filename, PATHINFO_FILENAME) . '_' . $count . '.' . pathinfo($filename, PATHINFO_EXTENSION);
                }

                // Simpan file di storage/app/public/uploads
                $path = $uploadedFile->storeAs('public/uploads', $newFilename);

                // Simpan file terkait di database
                $FeedbackFile = new CollectFile();
                $FeedbackFile->filename = $newFilename;
                $FeedbackFile->link = str_replace('public/', '', $path);
                $FeedbackFile->collectable_id = $komatfeedback->id; // Menghubungkan file dengan feedback
                $FeedbackFile->collectable_type = KomatFeedback::class; // Tipe polimorfik
                $FeedbackFile->save();
            }

            // Buat notifikasi sistem
            $document->notifsystem()->create([
                'status' => 'unread',
                'idunit' => $unit->id,
                'infostatus' => 'New comment added',
                'notifarray' => json_encode(['type' => 'comment', 'message' => 'New comment added by ' . $user->name]),
            ]);

            // Log aktivitas
            $document->systemLogs()->create([
                'message' => json_encode([
                    'message' => 'Komentar baru ditambahkan',
                    'datasebelum' => '',
                    'datasesudah' => $komatfeedback,
                ]),
                'level' => 'info',
                'user' => $userName,
                'user_id' => $user->id,
                'aksi' => 'comment_addition',
            ]);

            // Kirim notifikasi WhatsApp (opsional)
            $pesan = "Komentar baru ditambahkan untuk Komat Requirement: {$komatHistReq->komatRequirement->name} oleh {$user->name}:\n\n" .
                "💬 *{$request->input('comment')}*\n\n" .
                "Unit: {$unit->name}\n" .
                "🚀 *Silakan cek dokumen di sistem!*";
            TelegramService::ujisendunit($unit->name, $pesan);

            DB::commit();
            return redirect()->route('komatprocesshistory.show', $document->id)
                ->with('success', 'Komentar berhasil ditambahkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'Gagal menambahkan komentar: ' . $e->getMessage()]);
        }
    }
    public function addResumeFeedback(Request $request, $id, $komatHistReqId, $unitId)
    {
        // Validasi input
        $request->validate([
            'comment' => 'required|string|max:1000',
            'feedback_status' => 'required|in:draft,approved,notapproved,withremarks',
            'file.*' => 'nullable|file', // Validasi file
        ]);

        $user = auth()->user();
        $userName = $user->name;
        $document = KomatProcessHistory::findOrFail($id);
        $komatHistReq = KomatHistReq::findOrFail($komatHistReqId);
        $unit = Unit::findOrFail($unitId);

        // Pastikan dokumen masih terbuka
        if ($document->status !== 'Terbuka') {
            return redirect()->back()->with('error', 'Cannot add feedback to a closed document.');
        }

        // Pastikan komatHistReq terkait dengan dokumen dan unit memiliki posisi resume
        $komatPosition = KomatPosition::firstOrCreate(
            [
                'komat_hist_req_id' => $komatHistReq->id,
                'unit_id' => $unit->id,
                'level' => 'resume',
            ],
            [
                'status' => 'draft',
            ]
        );

        DB::beginTransaction();
        try {
            // Buat feedback baru untuk resume
            $komatFeedback = KomatFeedback::create([
                'komat_position_id' => $komatPosition->id,
                'komat_process_history_id' => $document->id,
                'komat_requirement_id' => $komatHistReq->komat_requirement_id,
                'comment' => $request->input('comment'),
                'status' => 'draft',
                'feedback_status' => $request->input('feedback_status'),
                'user_id' => $user->id,
                'user_rule' => $user->rule,
                'user_name' => $user->name,
            ]);

            // Handle file uploads
            foreach ($request->file('file', []) as $uploadedFile) {
                // Dapatkan nama file yang diunggah
                $filename = $uploadedFile->getClientOriginalName();
                // Dapatkan ekstensi file
                $fileFormat = $uploadedFile->getClientOriginalExtension();
                // Hapus ekstensi file dari nama file
                $filenameWithoutExtension = pathinfo($filename, PATHINFO_FILENAME);
                // Gabungkan nama file (tanpa ekstensi), nama pengguna, dan format file
                $filenameWithUserAndFormat = $filenameWithoutExtension . '_' . $userName . '.' . $fileFormat;
                $filename = $filenameWithUserAndFormat;

                // Periksa apakah nama file sudah ada
                $count = 0;
                $newFilename = $filename;
                while (CollectFile::where('filename', $newFilename)->exists()) {
                    $count++;
                    $newFilename = pathinfo($filename, PATHINFO_FILENAME) . '_' . $count . '.' . pathinfo($filename, PATHINFO_EXTENSION);
                }

                // Simpan file di storage/app/public/uploads
                $path = $uploadedFile->storeAs('public/uploads', $newFilename);

                // Simpan file terkait di database
                $feedbackFile = new CollectFile();
                $feedbackFile->filename = $newFilename;
                $feedbackFile->link = str_replace('public/', '', $path);
                $feedbackFile->collectable_id = $komatFeedback->id;
                $feedbackFile->collectable_type = KomatFeedback::class;
                $feedbackFile->save();
            }

            // Buat notifikasi sistem
            $document->notifsystem()->create([
                'status' => 'unread',
                'idunit' => $unit->id,
                'infostatus' => 'New resume feedback added',
                'notifarray' => json_encode(['type' => 'resume_feedback', 'message' => 'New resume feedback added by ' . $user->name]),
            ]);

            // Log aktivitas
            $document->systemLogs()->create([
                'message' => json_encode([
                    'message' => 'Resume feedback baru ditambahkan',
                    'datasebelum' => '',
                    'datasesudah' => $komatFeedback,
                ]),
                'level' => 'info',
                'user' => $userName,
                'user_id' => $user->id,
                'aksi' => 'resume_feedback_addition',
            ]);

            // Kirim notifikasi WhatsApp (opsional)
            $pesan = "Resume feedback baru ditambahkan untuk Komat Requirement: {$komatHistReq->komatRequirement->name} oleh {$user->name}:\n\n" .
                "💬 *{$request->input('comment')}*\n\n" .
                "Unit: {$unit->name}\n" .
                "🚀 *Silakan cek dokumen di sistem!*";
            TelegramService::ujisendunit($unit->name, $pesan);

            DB::commit();
            return redirect()->route('komatprocesshistory.show', $document->id)
                ->with('success', 'Resume feedback berhasil ditambahkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'Gagal menambahkan resume feedback: ' . $e->getMessage()]);
        }
    }


    public function addMTPRFeedback(Request $request, $id, $komatHistReqId, $unitId)
    {
        // Validasi input
        $request->validate([
            'comment' => 'required|string|max:1000',
            'feedback_status' => 'required|in:draft,approved,notapproved,withremarks',
            'file.*' => 'nullable|file', // Validasi file

        ]);

        $user = auth()->user();
        $userName = $user->name;
        $document = KomatProcessHistory::findOrFail($id);
        $komatHistReq = KomatHistReq::findOrFail($komatHistReqId);
        $unit = Unit::findOrFail($unitId);

        // Pastikan dokumen masih terbuka
        if ($document->status !== 'Terbuka') {
            return redirect()->back()->with('error', 'Cannot add feedback to a closed document.');
        }


        // Pastikan komatHistReq terkait dengan dokumen dan unit memiliki posisi resume
        $komatPosition = KomatPosition::firstOrCreate(
            [
                'komat_hist_req_id' => $komatHistReq->id,
                'unit_id' => $unit->id,
                'level' => 'mtpr_review',
            ],
            [
                'status' => $request->input('feedback_status'),
                'status_process' => 'done', // Atur status proses sesuai kebutuhan
            ]
        );

        DB::beginTransaction();
        try {
            // Buat feedback baru untuk resume
            $komatFeedback = KomatFeedback::create([
                'komat_position_id' => $komatPosition->id,
                'komat_process_history_id' => $document->id,
                'komat_requirement_id' => $komatHistReq->komat_requirement_id,
                'comment' => $request->input('comment'),
                'status' => 'reviewed',
                'feedback_status' => $request->input('feedback_status'),
                'user_id' => $user->id,
                'user_rule' => $user->rule,
                'user_name' => $user->name,
            ]);

            // Handle file uploads
            foreach ($request->file('file', []) as $uploadedFile) {
                // Dapatkan nama file yang diunggah
                $filename = $uploadedFile->getClientOriginalName();
                // Dapatkan ekstensi file
                $fileFormat = $uploadedFile->getClientOriginalExtension();
                // Hapus ekstensi file dari nama file
                $filenameWithoutExtension = pathinfo($filename, PATHINFO_FILENAME);
                // Gabungkan nama file (tanpa ekstensi), nama pengguna, dan format file
                $filenameWithUserAndFormat = $filenameWithoutExtension . '_' . $userName . '.' . $fileFormat;
                $filename = $filenameWithUserAndFormat;

                // Periksa apakah nama file sudah ada
                $count = 0;
                $newFilename = $filename;
                while (CollectFile::where('filename', $newFilename)->exists()) {
                    $count++;
                    $newFilename = pathinfo($filename, PATHINFO_FILENAME) . '_' . $count . '.' . pathinfo($filename, PATHINFO_EXTENSION);
                }

                // Simpan file di storage/app/public/uploads
                $path = $uploadedFile->storeAs('public/uploads', $newFilename);

                // Simpan file terkait di database
                $feedbackFile = new CollectFile();
                $feedbackFile->filename = $newFilename;
                $feedbackFile->link = str_replace('public/', '', $path);
                $feedbackFile->collectable_id = $komatFeedback->id;
                $feedbackFile->collectable_type = KomatFeedback::class;
                $feedbackFile->save();
            }

            // Buat notifikasi sistem
            $document->notifsystem()->create([
                'status' => 'unread',
                'idunit' => $unit->id,
                'infostatus' => 'New resume feedback added',
                'notifarray' => json_encode(['type' => 'resume_feedback', 'message' => 'New resume feedback added by ' . $user->name]),
            ]);

            // Log aktivitas
            $document->systemLogs()->create([
                'message' => json_encode([
                    'message' => 'Resume feedback baru ditambahkan',
                    'datasebelum' => '',
                    'datasesudah' => $komatFeedback,
                ]),
                'level' => 'info',
                'user' => $userName,
                'user_id' => $user->id,
                'aksi' => 'resume_feedback_addition',
            ]);

            // Kirim notifikasi WhatsApp (opsional)
            $pesan = "Resume feedback baru ditambahkan untuk Komat Requirement: {$komatHistReq->komatRequirement->name} oleh {$user->name}:\n\n" .
                "💬 *{$request->input('comment')}*\n\n" .
                "Unit: {$unit->name}\n" .
                "🚀 *Silakan cek dokumen di sistem!*";
            TelegramService::ujisendunit($unit->name, $pesan);

            DB::commit();
            return redirect()->route('komatprocesshistory.show', $document->id)
                ->with('success', 'Resume feedback berhasil ditambahkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'Gagal menambahkan resume feedback: ' . $e->getMessage()]);
        }
    }

    public function promoteFeedbackStatus(Request $request, $id, $komatHistReqId, $unitId, $feedbackId, $level)
    {
        $document = KomatProcessHistory::findOrFail($id);
        $komatHistReq = KomatHistReq::findOrFail($komatHistReqId);
        $unit = Unit::findOrFail($unitId);
        $feedback = KomatFeedback::findOrFail($feedbackId);

        // Pastikan feedback terkait dengan komatHistReq dan unit
        $komatPosition = KomatPosition::where('komat_hist_req_id', $komatHistReq->id)
            ->where('unit_id', $unit->id)
            ->where('level', $level)
            ->firstOrFail();

        // Pastikan user memiliki otorisasi (opsional, tambahkan middleware jika diperlukan)
        $user = auth()->user();
        $userName = $user->name;

        DB::beginTransaction();
        try {
            // Logika promosi status
            if ($feedback->status === 'draft') {
                $feedback->status = 'reviewed';
                $feedback->save();

                // Log aktivitas
                $document->systemLogs()->create([
                    'message' => json_encode([
                        'message' => 'Feedback dipromosikan ke reviewed',
                        'datasebelum' => 'draft',
                        'datasesudah' => 'reviewed',
                    ]),
                    'level' => 'info',
                    'user' => $userName,
                    'user_id' => $user->id,
                    'aksi' => 'feedback_status_promotion',
                ]);

                // Kirim notifikasi WhatsApp
                $pesan = "Feedback untuk Komat Requirement: {$komatHistReq->komatRequirement->name} telah dipromosikan ke *reviewed* oleh {$user->name}:\n\n" .
                    "💬 *{$feedback->comment}*\n\n" .
                    "Unit: {$unit->name}\n" .
                    "🚀 *Silakan cek dokumen di sistem!*";
                TelegramService::ujisendunit($unit->name, $pesan);
            } elseif ($feedback->status === 'reviewed') {
                $feedback->status = 'last_accepted';
                $feedback->save();

                // Update status KomatPosition ke approved
                $komatPosition->status = $feedback->feedback_status;
                $komatPosition->status_process = 'done';
                $komatPosition->save();

                // Log aktivitas
                $document->systemLogs()->create([
                    'message' => json_encode([
                        'message' => 'Feedback dipromosikan ke last_accepted dan KomatPosition diubah ke approved',
                        'datasebelum' => 'reviewed',
                        'datasesudah' => 'last_accepted',
                    ]),
                    'level' => 'info',
                    'user' => $userName,
                    'user_id' => $user->id,
                    'aksi' => 'feedback_status_promotion',
                ]);

                // Kirim notifikasi WhatsApp
                $pesan = "Feedback untuk Komat Requirement: {$komatHistReq->komatRequirement->name} telah dipromosikan ke *last_accepted* oleh {$user->name}:\n\n" .
                    "💬 *{$feedback->comment}*\n\n" .
                    "Unit: {$unit->name}\n" .
                    "✅ *Status Diskusi: Approved*\n" .
                    "🚀 *Silakan cek dokumen di sistem!*";
                TelegramService::ujisendunit($unit->name, $pesan);
            } else {
                throw new \Exception('Status feedback tidak valid untuk promosi.');
            }

            DB::commit();
            return redirect()->route('komatprocesshistory.show', $document->id)
                ->with('success', 'Status feedback berhasil dipromosikan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'Gagal mempromosikan status feedback: ' . $e->getMessage()]);
        }
    }

    public function deleteFeedback(Request $request, $id, $komatHistReqId, $unitId, $feedbackId, $level)
    {
        $document = KomatProcessHistory::findOrFail($id);
        $komatHistReq = KomatHistReq::findOrFail($komatHistReqId);
        $unit = Unit::findOrFail($unitId);
        $feedback = KomatFeedback::findOrFail($feedbackId);

        // Authorization check: Ensure the authenticated user's unit_id matches the document's unit_distributor_id
        $user = auth()->user();
        if ($document->unit_distributor_id !== $user->unit_id) {
            return redirect()->back()->withErrors(['error' => 'Anda tidak memiliki otorisasi untuk menghapus feedback ini.']);
        }

        // Pastikan feedback terkait dengan komatHistReq dan unit
        $komatPosition = KomatPosition::where('komat_hist_req_id', $komatHistReq->id)
            ->where('unit_id', $unit->id)
            ->where('level', $level)
            ->firstOrFail();

        DB::beginTransaction();
        try {
            // Delete associated files
            foreach ($feedback->files as $file) {
                // Delete file from storage
                Storage::delete('public/uploads/' . $file->filename);
                // Delete file record from database
                $file->delete();
            }

            // Store feedback data for logging before deletion
            $feedbackData = $feedback->toArray();
            $feedback->delete();

            // Log activity
            $document->systemLogs()->create([
                'message' => json_encode([
                    'message' => 'Feedback dihapus',
                    'datasebelum' => $feedbackData,
                    'datasesudah' => null,
                ]),
                'level' => 'info',
                'user' => $user->name,
                'user_id' => $user->id,
                'aksi' => 'feedback_deletion',
            ]);

            // Send WhatsApp notification
            $pesan = "Feedback untuk Komat Requirement: {$komatHistReq->komatRequirement->name} telah dihapus oleh {$user->name}:\n\n" .
                "💬 *{$feedbackData['comment']}*\n\n" .
                "Unit: {$unit->name}\n" .
                "🚀 *Silakan cek dokumen di sistem!*";
            TelegramService::ujisendunit($unit->name, $pesan);

            DB::commit();
            return redirect()->route('komatprocesshistory.show', $document->id)
                ->with('success', 'Feedback berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'Gagal menghapus feedback: ' . $e->getMessage()]);
        }
    }

    // Controller
    public function copyTo(Request $request, $id, $level)
    {
        Log::info('Starting copyTo method', [
            'id' => $id,
            'level' => $level,
            'sendto' => $request->input('sendto'),
        ]);

        $document = KomatProcessHistory::findOrFail($id);
        $user = auth()->user();
        $userName = $user->name;
        $sendto = $request->input('sendto');
        $unit = Unit::where('name', $sendto)->firstOrFail();

        Log::info('Document and unit details', [
            'document_id' => $document->id,
            'unit_distributor_id' => $document->unit_distributor_id,
            'target_unit_id' => $unit->id,
            'target_unit_name' => $unit->name,
        ]);

        DB::beginTransaction();
        try {
            $copiedFeedbacks = [];
            $resumePosition = null;

            foreach ($document->komatHistReqs as $komatHistReq) {
                $resumePosition = $komatHistReq->komatPositions->where('level', 'resume')->first();
                Log::info('Checking resume position', [
                    'komat_hist_req_id' => $komatHistReq->id,
                    'resume_position_exists' => $resumePosition ? true : false,
                    'resume_position_status' => $resumePosition ? $resumePosition->status : null,
                ]);

                if ($resumePosition && $resumePosition->feedbacks->where('status', 'last_accepted')->isNotEmpty()) {
                    $smPosition = KomatPosition::firstOrCreate([
                        'komat_hist_req_id' => $komatHistReq->id,
                        'unit_id' => $unit->id,
                        'level' => $level,
                    ], [
                        'status' => 'draft',
                        'status_process' => 'not_started',
                    ]);

                    $lastFeedback = $resumePosition->feedbacks->where('status', 'last_accepted')->first();
                    $files = $lastFeedback->files;

                    $newFeedback = KomatFeedback::create([
                        'komat_position_id' => $smPosition->id,
                        'komat_process_history_id' => $document->id,
                        'komat_requirement_id' => $komatHistReq->komat_requirement_id,
                        'comment' => $lastFeedback->comment,
                        'feedback_status' => $lastFeedback->feedback_status,
                        'status' => 'reviewed',
                        'user_id' => $user->id,
                        'user_rule' => $user->rule,
                        'user_name' => $user->name,
                    ]);

                    foreach ($files as $file) {
                        CollectFile::create([
                            'filename' => $file->filename,
                            'link' => $file->link,
                            'collectable_id' => $newFeedback->id,
                            'collectable_type' => KomatFeedback::class,
                        ]);
                    }

                    $copiedFeedbacks[] = [
                        'komat_requirement' => $komatHistReq->komatRequirement->name,
                        'comment' => $lastFeedback->comment,
                        'unit_id' => $document->unit_distributor_id,
                    ];
                }
            }

            if (empty($copiedFeedbacks)) {
                Log::warning('No valid resume feedback found to copy', ['document_id' => $document->id]);
                throw new \Exception('No valid resume feedback found to copy.');
            }

            // If sent to MTPR (unit 31), regenerate no_dokumen if resume position is approved or withremarks
            if ($unit->id === 31) {
                Log::info('Checking conditions for no_dokumen regeneration', [
                    'resume_position_exists' => $resumePosition ? true : false,
                    'resume_position_status' => $resumePosition ? $resumePosition->status : null,
                    'unit_distributor_id' => $document->unit_distributor_id,
                ]);

                if ($resumePosition && in_array($resumePosition->status, ['approved', 'withremarks'])) {
                    Log::info('Regenerating no_dokumen for MTPR', [
                        'document_id' => $document->id,
                        'unit_distributor_id' => $document->unit_distributor_id,
                    ]);

                    // Regenerate no_dokumen
                    $document->no_prefix = $document->no_prefix ?? 'AP';
                    $document->no_year = $document->no_year ?? date('Y');
                    $document->no_midcode = $document->no_midcode ?? KomatProcessHistory::determineMidCode($document->unit_distributor_id);

                    if ($document->no_midcode) {
                        $document->no_counter = KomatProcessHistory::nextCounter($document->no_midcode, $document->no_year);
                        $document->no_dokumen = "{$document->no_prefix}"
                            . str_pad($document->no_counter, 3, '0', STR_PAD_LEFT)
                            . "/{$document->no_midcode}/{$document->no_year}";

                        Log::info('Regenerated no_dokumen', [
                            'document_id' => $document->id,
                            'no_dokumen' => $document->no_dokumen,
                            'no_prefix' => $document->no_prefix,
                            'no_counter' => $document->no_counter,
                            'no_midcode' => $document->no_midcode,
                            'no_year' => $document->no_year,
                        ]);
                    } else {
                        Log::warning('Failed to regenerate no_dokumen: no valid midcode', [
                            'document_id' => $document->id,
                            'unit_distributor_id' => $document->unit_distributor_id,
                        ]);
                    }
                } else {
                    Log::info('Skipping no_dokumen regeneration: resume position not approved or withremarks', [
                        'resume_position_status' => $resumePosition ? $resumePosition->status : null,
                    ]);
                }
            }

            // Save the document with all changes
            Log::info('Before saving document', [
                'document_id' => $document->id,
                'no_dokumen' => $document->no_dokumen,
                'unit_distributor_id' => $document->unit_distributor_id,
            ]);
            $document->save();
            Log::info('Document saved with new no_dokumen', [
                'document_id' => $document->id,
                'no_dokumen' => $document->no_dokumen,
            ]);

            // Create notification
            $document->notifsystem()->create([
                'status' => 'unread',
                'idunit' => $unit->id,
                'infostatus' => 'New feedback copied to ' . $level,
                'notifarray' => json_encode([
                    'type' => 'feedback_copy',
                    'message' => 'Feedback copied to ' . $level . ' by ' . $user->name,
                ]),
            ]);

            // Log activity
            $document->systemLogs()->create([
                'message' => json_encode([
                    'message' => 'Feedback berhasil disalin ke level ' . $level,
                    'datasebelum' => '',
                    'datasesudah' => $copiedFeedbacks,
                ]),
                'level' => 'info',
                'user' => $userName,
                'user_id' => $user->id,
                'aksi' => 'feedback_copy',
            ]);

            // Send Telegram/WhatsApp notification
            $pesan = "Feedback telah disalin ke level *{$level}* untuk Komat Process History ID: {$document->id} oleh {$user->name}:\n\n";
            foreach ($copiedFeedbacks as $feedback) {
                $pesan .= "📄 Komat Requirement: {$feedback['komat_requirement']}\n";
                $pesan .= "💬 Komentar: {$feedback['comment']}\n";
                $pesan .= "Unit: {$unit->name}\n\n";
            }
            $pesan .= "🚀 *Silakan cek dokumen di sistem!*";

            Log::info('Sending Telegram notification', [
                'unit_name' => $unit->name,
                'message' => $pesan,
            ]);

            if ($unit->name != "MTPR") {
                TelegramService::ujisendunit($unit->name, $pesan);
            } else {
                TelegramService::sendTeleMessage(['6285335086789'], $pesan);
            }

            DB::commit();
            Log::info('copyTo method completed successfully', ['document_id' => $document->id]);

            return response()->json([
                'success' => true,
                'message' => 'Feedback successfully copied to ' . $level,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to copy feedback', [
                'document_id' => $document->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'error' => 'Gagal menyalin feedback: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function index()
    {
        // Load all ProjectType records for tabs
        $projects = ProjectType::all();
        $units = Unit::where('name', 'NOT LIKE', '%Manager%')
            ->where('name', '!=', 'MTPR')
            ->where('is_technology_division', 1)
            ->get();

        // Load KomatProcess with related data, ordered by supplier name
        $komatProcesses = KomatProcess::with([
            'newbomkomat',
            'komatProcessHistories.requirement',
            'komatProcessHistories.komatHistReqs.komatPositions',
            'komatProcessHistories.komatHistReqs.komatRequirement',
            'komatProcessHistories.unitDistributor',
            'komatProcessHistories.supplier',
            'komatProcessHistories.projectType',
        ])
            ->leftJoin('komat_process_history', 'komat_process.id', '=', 'komat_process_history.komat_process_id')
            ->leftJoin('komat_supplier', 'komat_process_history.komat_supplier_id', '=', 'komat_supplier.id')
            ->leftJoin('komat_requirement', 'komat_process_history.komat_requirement_id', '=', 'komat_requirement.id')
            ->select('komat_process.*')
            ->orderBy('komat_supplier.name', 'asc')
            ->orderBy('komat_requirement.name', 'asc')
            ->groupBy('komat_process.id', 'komat_process.komat_name', 'komat_process.komat_id', 'komat_process.created_at', 'komat_process.updated_at')
            ->get();


        $authuser = auth()->user();


        $projectpics = [
            "Quality Engineering" => ['id' => 14, 'singkatan' => 'QE'],
            "Electrical Engineering System" => ['id' => 3, 'singkatan' => 'EES'],
            "Mechanical Engineering System" => ['id' => 4, 'singkatan' => 'MES'],
            "Product Engineering" => ['id' => 2, 'singkatan' => 'PE'],
            "Desain Mekanik & Interior" => ['id' => 5, 'singkatan' => 'DMI'],
            "Desain Carbody" => ['id' => 7, 'singkatan' => 'DC'],
            "RAMS" => ['id' => 13, 'singkatan' => 'RAMS'],
            "Desain Bogie & Wagon" => ['id' => 6, 'singkatan' => 'DBW'],
            "Desain Elektrik" => ['id' => 8, 'singkatan' => 'DE'],
            "Preparation & Support" => ['id' => 9, 'singkatan' => 'PS'],
            "Welding Technology" => ['id' => 10, 'singkatan' => 'WT'],
            "Shop Drawing" => ['id' => 11, 'singkatan' => 'SD'],
            "Teknologi Proses" => ['id' => 12, 'singkatan' => 'TP'],
        ];
        return view('komatprocesshistory.index', compact('komatProcesses', 'units', 'authuser', 'projects', 'units', 'projectpics'));
    }


    public function addRequirement(Request $request)
    {
        try {
            $request->validate([
                'name' => ['required', 'string', 'max:255', 'regex:/^\S+$/'], // Ensure no spaces
                'description' => ['nullable', 'string'],
            ]);

            $requirement = KomatRequirement::create([
                'name' => $request->name,
                'description' => $request->description,
            ]);

            return response()->json([
                'success' => true,
                'requirement' => $requirement,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
