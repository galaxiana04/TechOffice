<?php

namespace App\Http\Controllers;

use App\Models\MemoSekdiv;
use App\Models\MemoSekdivSmDecision;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\MemosekdivFeedback;
use App\Models\MemosekdivTimeline;
use App\Models\MemoSekdivAccess;
use App\Models\Wagroupnumber;
use App\Services\TelegramService;
use App\Models\CollectFile;
use App\Models\Unit;
use App\Models\ProjectType;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MemoSekdivController extends Controller
{
    public function index()
    {
        $memosekdivs = MemoSekdiv::with(['projectType', 'smDecisions.unitUnderSms', 'feedbacks'])->get();
        foreach ($memosekdivs as $memosekdiv) {
            $memosekdiv = $memosekdiv->detailonedocument();
        }


        $authuser = auth()->user();
        return view('memosekdivs.index', compact('memosekdivs', 'authuser'));
    }

    public function create()
    {
        $projectTypes = ProjectType::all();
        $smDecisions = ['Senior Manager Desain', 'Senior Manager Engineering', 'Senior Manager Teknologi Produksi', 'Manager MTPR']; // Sesuaikan dengan enum di database
        return view('memosekdivs.create', compact('projectTypes', 'smDecisions'));
    }



    public function store(Request $request)
    {
        $request->validate([
            'documentname' => 'required|string|max:255',
            'documentkind' => 'required|string|max:255',
            'documentnumber' => 'required|string|max:255',
            'project_type_id' => 'nullable|integer|exists:project_types,id',
            'smdecisions' => 'required|array',
            'condition1' => 'nullable|string',
            'condition2' => 'nullable|string',
            'file.*' => 'file|mimes:pdf,doc,docx,xlsx,pptx,jpg,jpeg,png|max:10240',
        ]);

        DB::beginTransaction(); // <-- Mulai transaksi
        $smpositionarray = [
            'Senior Manager Engineering' => 15,
            'Senior Manager Desain' => 114,
            'Senior Manager Teknologi Produksi' => 116,
            'Manager MTPR' => 85,
        ];


        try {
            $user = auth()->user();
            $existingDoc = MemoSekdiv::where('documentnumber', $request->input('documentnumber'))
                ->where('project_type_id', $request->input('project_type_id'))
                ->exists();

            if ($existingDoc) {
                return response()->json(['Message' => "Sudah pernah diiput"]);
            }



            $document = MemoSekdiv::create([
                'documentname' => $request->documentname,
                'documentnumber' => $request->documentnumber,
                'project_type_id' => $request->project_type_id ?? null,
                'documentstatus' => 'open',
                'documentkind' => $request->documentkind,
            ]);

            $document->memoSekdivAccesses()->createMany([
                ['user_id' => 1, 'permission_user_id' => $user->id],
                ['user_id' => 193, 'permission_user_id' => $user->id],
            ]);

            $units = Unit::whereIn('name', $request->smdecisions)->get()->keyBy('name');
            $aksesData = [];
            $smDecisionData = [];
            $notifSystemData = [];
            foreach ($request->smdecisions as $smname) {




                $aksesData[] = [
                    'user_id' => $smpositionarray[$smname],
                    'permission_user_id' => $user->id,
                    'memo_sekdiv_id' => $document->id,
                ];
                $smDecisionData[] = [
                    'memo_sekdiv_id' => $document->id,
                    'smpositionname' => $smname,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];


                if ($units->has($smname)) {
                    $unit = $units[$smname]; // <-- Perbaikan: Ambil unit dari koleksi
                    $notifSystemData[] = [
                        'notifmessage_id' => $document->id,
                        'notifmessage_type' => MemoSekdiv::class,
                        'status' => 'unread',
                        'idunit' => $unit->id,
                        'infostatus' => 'User dont read this message',
                        'notifarray' => json_encode(['type' => 'order', 'message' => 'Order received']),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }
            MemoSekdivSmDecision::insert($smDecisionData);
            MemoSekdivAccess::insert($aksesData);
            Notification::insert($notifSystemData);
            MemosekdivTimeline::create([
                'memo_sekdiv_id' => $document->id,
                'infostatus' => 'documentopened',
                'entertime' => now(),
            ]);

            $feedback = MemosekdivFeedback::create([
                'memo_sekdiv_id' => $document->id,
                'pic' => $user->rule,
                'author' => $user->name,
                'level' => "pembukadokumen",
                'email' => $user->email,
                'isread' => true,
                'reviewresult' => null,
                'comment' => null,
                'condition1' => null,
                'condition2' => null,
            ]);

            $userName = $user->name;
            $listcollectable_id = [];
            $fileInsertData = [];
            $list = '';

            if ($request->hasFile('file')) {
                foreach ($request->file('file') as $uploadedFile) {
                    $filename = $uploadedFile->getClientOriginalName();
                    $fileFormat = $uploadedFile->getClientOriginalExtension();
                    $filenameWithoutExtension = pathinfo($filename, PATHINFO_FILENAME);
                    $filename = $filenameWithoutExtension . '_' . $userName . '.' . $fileFormat;

                    $count = 0;
                    $newFilename = $filename;
                    while (CollectFile::where('filename', $newFilename)->exists()) {
                        $count++;
                        $newFilename = pathinfo($filename, PATHINFO_FILENAME) . '_' . $count . '.' . pathinfo($filename, PATHINFO_EXTENSION);
                    }

                    $path = $uploadedFile->storeAs('public/uploads', $newFilename);


                    $fileInsertData[] = [
                        'filename' => $newFilename,
                        'link' => str_replace('public/', '', $path),
                        'collectable_id' => $feedback->id,
                        'collectable_type' => MemosekdivFeedback::class,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];

                    $listcollectable_id[] = $feedback->id;
                }
                CollectFile::insert($fileInsertData);
            }


            $files = CollectFile::whereIn('collectable_id', $listcollectable_id)->get();

            foreach ($files as $file) {
                $list .= "📄 *" . $file->filename . "* ➡️ 🔗 Downloadfile_" . $file->id . "\n";
            }

            $pesan = 'Memo Sekdiv: ' . $request->input('documentname') . ' telah dibuka dan ditunjukan ke unit :' . $document->operator . "\n\n" .
                "📂 Berikut daftar dokumen yang bisa diunduh by WhatsApp:\n" .
                $list .
                "\n🚀 *Ayo segera dikerjakan!* Terima kasih atas kerjasamanya! 🙏😊";

            // Ambil ID berdasarkan smdecisions
            $userIds = collect($request->smdecisions)->map(function ($smname) use ($smpositionarray) {
                return $smpositionarray[$smname] ?? null;
            })->filter()->unique()->values();

            // Ambil semua user hanya sekali
            $users = User::whereIn('id', $userIds)->pluck('waphonenumber')->filter()->values()->all();

            // Kirim pesan WhatsApp
            TelegramService::sendTeleMessage($users, $pesan);

            $document->systemLogs()->create([
                'message' => json_encode([
                    'message' => 'Memo Sekdiv berhasil dibuat',
                    'datasebelum' => '',
                    'datasesudah' => $document,
                ]),
                'level' => 'info',
                'user' => $user->name,
                'user_id' => $user->id,
                'aksi' => 'memosekdivaddition',
            ]);

            DB::commit(); // <-- Commit jika berhasil
            return redirect()->route('memosekdivs.index')->with('success', 'Memo Sekdiv created successfully.');
        } catch (\Exception $e) {
            DB::rollBack(); // <-- Rollback jika gagal
            return back()->withErrors(['error' => 'Terjadi kesalahan saat menyimpan: ' . $e->getMessage()]);
        }
    }

    public function showDocument(Request $request, $id)
    {
        $auth = auth()->user();
        $nama_divisi = $auth->rule;
        $users = User::select('id', 'name')->get(); // Optimized to select only needed columns

        $userid = $auth->id;
        $statussetujulist = [];
        // Mencari dokumen dengan eager loading untuk relasi yang diperlukan
        $document = MemoSekdiv::with(['feedbacks', 'timelines'])->findOrFail($id);
        // Cek apakah user memiliki akses
        $hasAccess = $document->memoSekdivAccesses->contains('user_id', $userid);

        if (!$hasAccess) {
            return response()->json([
                'message' => 'Anda tidak memiliki akses ke dokumen ini.'
            ], 403);
        }

        $projectname = '-'; // default jika tidak ada tipe proyek

        if ($document->project_type_id) {
            $projectType = ProjectType::find($document->project_type_id);
            if ($projectType) {
                $projectname = $projectType->title;
            } else {
                $projectname = "Tidak Terikat Proyek";
            }
        }
        // // Mendapatkan detail dari dokumen
        $document = $document->detailonedocument();




        $yourrule = $nama_divisi;

        $sminvolved = [];
        foreach ($document->smunitpicvalidation as $key => $value) {
            $sminvolved[] = $key;
        }


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



        // Mengembalikan tampilan dengan data yang diperlukan
        return view('memosekdivs.show.show', compact(
            'document',
            'projectname',
            'yourrule',
            'sminvolved',
            'users',
            'hasAccess'
        ));
    }

    public function storeAccess(Request $request)
    {
        $request->validate([
            'memo_sekdiv_id' => 'required|exists:memosekdivs,id',
            'user_id' => 'required|exists:users,id',
        ]);

        try {
            DB::beginTransaction();

            // Cek apakah pengguna sudah memiliki akses
            $existingAccess = MemoSekdivAccess::where('memo_sekdiv_id', $request->memo_sekdiv_id)
                ->where('user_id', $request->user_id)
                ->exists();

            if ($existingAccess) {
                return response()->json(['message' => 'Pengguna sudah memiliki akses'], 422);
            }

            MemoSekdivAccess::create([
                'memo_sekdiv_id' => $request->memo_sekdiv_id,
                'user_id' => $request->user_id,
                'permission_user_id' => auth()->user()->id,
            ]);

            DB::commit();
            return response()->json(['message' => 'Akses berhasil ditambahkan'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Gagal menambahkan akses: ' . $e->getMessage()], 500);
        }
    }

    public function listAccess($documentId)
    {
        $document = MemoSekdiv::with('memoSekdivAccesses.user')->findOrFail($documentId);
        $accesses = $document->memoSekdivAccesses->map(function ($access) {
            return [
                'user_id' => $access->user_id,
                'name' => $access->user ? $access->user->name : null,
            ];
        })->filter(function ($access) {
            return !is_null($access['name']);
        })->values()->toArray();

        return response()->json(['accesses' => $accesses]);
    }

    public function memosekdivedit($id)
    {
        $document = MemoSekdiv::findOrFail($id);
        $document = $document->detailonedocument();
        $yourauth = auth()->user();
        $project_pic = [];
        foreach ($document->unitpicvalidation as $key => $unitpicvalidation) {
            $project_pic[] = $key;
        }

        if ($yourauth->rule == "Senior Manager Engineering") {
            $listpic = [
                'Product Engineering',
                'Mechanical Engineering System',
                'Electrical Engineering System',
                'Quality Engineering',
            ];
        } else if ($yourauth->rule == "Senior Manager Desain") {
            $listpic = [
                'Desain Mekanik & Interior',
                'Desain Bogie & Wagon',
                'Desain Carbody',
                'Desain Elektrik',
            ];
        } else if ($yourauth->rule == "Senior Manager Teknologi Produksi") {
            $listpic = [
                'Preparation & Support',
                'Welding Technology',
                'Shop Drawing',
                'Teknologi Proses'
            ];
        } else if ($yourauth->rule == 'Manager MTPR') {
            $listpic = [
                'MTPR',
                'RAMS',
            ];
        }


        return view('memosekdivs.editdokumen', compact('document', 'listpic', 'project_pic', 'yourauth'));
    }

    public function updateinformasimemo(Request $request, $id)
    {
        DB::beginTransaction();

        try {
            $document = MemoSekdiv::with('feedbacks')->findOrFail($id);
            $documentsebelum = $document;
            $user = auth()->user();
            $auth = $user->rule;
            $projectinput = $request->input('project_pic');
            $timeline = $document->timelines;
            $nama_divisi = $document->operator;

            $nama_divisi_share_read = $timeline->firstWhere('infostatus', $nama_divisi . '_share_read');
            if (!$nama_divisi_share_read) {
                MemoSekdivTimeline::create([
                    'memo_sekdiv_id' => $document->id,
                    'infostatus' => $nama_divisi . '_share_read',
                    'entertime' => now(),
                ]);
            }

            if ($request->has('project_pic')) {
                $document->update([
                    'documentname' => $request->input('documentname'),
                ]);

                $smDecision = $document->smDecisions->where('smpositionname', $auth)->first();
                $unitUnderSms = $smDecision->unitUnderSms;

                $unitNames = $unitUnderSms->pluck('unitname')->toArray();
                $missingUnits = array_diff($projectinput, $unitNames);
                $unitmanager = [
                    'MTPR' => 85,
                    'RAMS' => 85,
                    'Product Engineering' => 41,
                    'Mechanical Engineering System' => 20,
                    'Electrical Engineering System' => 27,
                    'Quality Engineering' => 94,
                    'Desain Mekanik & Interior' => 13,
                    'Desain Bogie & Wagon' => 18,
                    'Desain Carbody' => 47,
                    'Desain Elektrik' => 51,
                    'Teknologi Proses' => 116,
                    'Shop Drawing' => 117,
                    'Preparation & Support' => 158,
                    'Welding Technology' => 189,
                ];
                if (!empty($missingUnits)) {
                    $accessesData = [];
                    $unitsData = [];

                    foreach ($missingUnits as $missingUnit) {
                        $accessesData[] = [
                            'user_id' => $unitmanager[$missingUnit],
                            'permission_user_id' => $user->id,
                            'memo_sekdiv_id' => $document->id, // pastikan ini field foreign key yang sesuai di memoSekdivAccesses
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];

                        $unitsData[] = [
                            'memo_sekdiv_sm_decision_id' => $smDecision->id, // pastikan ini foreign key di unitUnderSms
                            'unitname' => $missingUnit,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }

                    // Bulk insert ke memoSekdivAccesses
                    if (!empty($accessesData)) {
                        $document->memoSekdivAccesses()->insert($accessesData);
                    }

                    // Bulk insert ke unitUnderSms
                    if (!empty($unitsData)) {
                        $smDecision->unitUnderSms()->insert($unitsData);
                    }
                }
                $listnohp = [];
                // Ambil semua user id yang ada di $unitmanager untuk unit yang ada di $projectinput
                $userIdsNeeded = [];
                foreach ($projectinput as $pic) {
                    if (isset($unitmanager[$pic])) {
                        $userIdsNeeded[] = $unitmanager[$pic];
                    }
                }
                $userIdsNeeded = array_unique($userIdsNeeded);
                $specialusers = User::whereIn('id', $userIdsNeeded)->get()->keyBy('id');

                foreach ($projectinput as $pic) {

                    try {
                        $namaFile = $request->input('documentname');
                        $unit = Unit::where('name', $pic)->first();
                        $existingFile = Notification::where('idunit', $unit->id)
                            ->where('notifmessage_id', $document->id)
                            ->where('notifarray->type', 'share')
                            ->first();

                        if (!$existingFile) {
                            // mail anggota unit
                            $document->notifsystem()->create([
                                'status' => 'unread',
                                'idunit' => $unit->id,
                                'infostatus' => 'User dont read this message',
                                'notifarray' => json_encode(['type' => 'share', 'message' => 'Order received']),
                            ]);

                            // mail manager unit
                            $managerUnit = Unit::where('name', 'Manager ' . $pic)->first();
                            $document->notifsystem()->create([
                                'status' => 'unread',
                                'idunit' => $managerUnit->id,
                                'infostatus' => 'User dont read this message',
                                'notifarray' => json_encode(['type' => 'share', 'message' => 'Order received']),
                            ]);

                            $feedbacks = $document->feedbacks;
                            $list = '';

                            if ($feedbacks->isNotEmpty()) {
                                $files = $feedbacks->last()->files;
                                foreach ($files as $file) {
                                    $list .= "📄 *" . $file->filename . "* ➡️ 🔗 Downloadfile_" . $file->id . "\n";
                                }
                            }

                            $pesan = "Memo Sekdiv " . $namaFile . " dikirimkan ke unit ini untuk dicek/dikerjakan.\n\n" .
                                "📂 Berikut daftar dokumen yang bisa diunduh by WhatsApp:\n" .
                                $list .
                                "\n🚀 *Ayo segera dikerjakan!* Terima kasih atas kerjasamanya! 🙏😊";



                            // Ambil nomor WA dari user yang sudah di-load
                            $userId = $unitmanager[$pic] ?? null;
                            if ($userId && isset($specialusers[$userId])) {
                                $listnohp[] = $specialusers[$userId]->waphonenumber;
                            }





                            $document->systemLogs()->create([
                                'message' => json_encode([
                                    'message' => 'Memo berhasil dikirim ke unit unit',
                                    'datasebelum' => '',
                                    'datasesudah' => $document,
                                ]),
                                'level' => 'info',
                                'user' => auth()->user()->name,
                                'user_id' => auth()->user()->id,
                                'aksi' => 'documentshare',
                            ]);
                        }
                    } catch (\Exception $e) {
                        // Optional: log error internal unit notification
                    }
                }


                TelegramService::sendTeleMessage($listnohp, $pesan);
            } else {
                $document->update([
                    'documentname' => $request->input('documentname'),
                ]);
            }

            $document->systemLogs()->create([
                'message' => json_encode([
                    'message' => 'Data ditambahkan',
                    'datasebelum' => $documentsebelum,
                    'datasesudah' => '',
                ]),
                'level' => 'info',
                'user' => auth()->user()->name,
                'user_id' => auth()->user()->id,
                'aksi' => 'unitpicaddition',
            ]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();

            $informasiupload = "Gagal mengupdate file: " . $e->getMessage();
            return redirect()->back()->with('error', $informasiupload);
        }


        return redirect()->route('memosekdivs.show', ['id' => $document->id]);
    }

    public function documentfeedback($id)
    {
        $document = MemoSekdiv::findOrFail($id);
        return view('memosekdivs.uploadfeedback', compact('document'));
    }
    public function documentmanagerfeedback($id)
    {
        $document = MemoSekdiv::findOrFail($id);
        return view('memosekdivs.uploadmanagerfeedback', compact('document'));
    }
    public function uploadreply($id)
    {
        $document = MemoSekdiv::findOrFail($id);
        return view('memosekdivs.uploadreply', compact('document'));
    }

    public function uploadsignaturefeedbackmerge(Request $request, $id)
    {
        DB::beginTransaction(); // <-- Mulai transaksi
        try {
            // Find the document to be updated
            $document = MemoSekdiv::findOrFail($id);

            // Update user information
            $user = auth()->user();
            $userName = $user->name;
            $userEmail = $user->email;
            $level = $request->input('level');

            if ($request->input('feedbacklevel')) {
                $pic = str_replace("Manager ", "", $user->rule);
            } else {
                $pic = $user->rule;
            }

            // Create new feedback
            $feedback = MemoSekdivFeedback::create([
                'memo_sekdiv_id' => $document->id,
                'pic' => $pic,
                'author' => $userName,
                'isread' => $request->input('isread'),
                'reviewresult' => $request->input('reviewresult'),
                'level' => $level,
                'email' => $userEmail,
                'comment' => $request->input('comment'),
                'condition1' => $request->input('condition1'),
                'condition2' => $request->input('condition2'),
            ]);

            // Handle file uploads
            if ($request->hasFile('file')) {
                foreach ($request->file('file') as $uploadedFile) {
                    $filename = $uploadedFile->getClientOriginalName();
                    $fileFormat = $uploadedFile->getClientOriginalExtension();
                    $filenameWithoutExtension = pathinfo($filename, PATHINFO_FILENAME);
                    $filenameWithUserAndFormat = $filenameWithoutExtension . '_' . $userName . '.' . $fileFormat;
                    $filename = $filenameWithUserAndFormat;

                    $count = 0;
                    $newFilename = $filename;
                    while (CollectFile::where('filename', $newFilename)->exists()) {
                        $count++;
                        $newFilename = pathinfo($filename, PATHINFO_FILENAME) . '_' . $count . '.' . pathinfo($filename, PATHINFO_EXTENSION);
                    }

                    $path = $uploadedFile->storeAs('public/uploads', $newFilename);

                    $newmemoFile = new CollectFile();
                    $newmemoFile->filename = $newFilename;
                    $newmemoFile->link = str_replace('public/', '', $path);
                    $newmemoFile->collectable_id = $feedback->id;
                    $newmemoFile->collectable_type = MemoSekdivFeedback::class;
                    $newmemoFile->save();
                }
            }

            if ($request->filecount > 0) {
                for ($i = 0; $i < $request->filecount; $i++) {
                    $newmemoFile = new CollectFile();
                    $newmemoFile->filename = "filekosong";
                    $newmemoFile->link = '';
                    $newmemoFile->collectable_id = $feedback->id;
                    $newmemoFile->collectable_type = MemoSekdivFeedback::class;
                    $newmemoFile->save();
                }
            }

            // Logging based on condition2 value
            if ($request->input('condition2') == "signature") {
                $pesan = 'Memo Sekdiv: ' . $document->id . ' berhasil diupdate dokumen dengan bertanda tangan.';
                $document->systemLogs()->create([
                    'message' => json_encode([
                        'message' => $pesan,
                        'datasebelum' => '',
                        'datasesudah' => $document,
                    ]),
                    'level' => 'info',
                    'user' => $user->name,
                    'user_id' => $user->id,
                    'aksi' => 'documentsignature',
                ]);
            } elseif ($request->input('condition2') == "combine") {
                $pesan = 'Memo Sekdiv: ' . $document->id . ' menerima finalisasi feedback oleh PE.';
                $pesansingkat = 'Finalisasi feedback diupload';
                $document->systemLogs()->create([
                    'message' => json_encode([
                        'message' => $pesan,
                        'datasebelum' => '',
                        'datasesudah' => $document,
                    ]),
                    'level' => 'info',
                    'user' => $user->name,
                    'user_id' => $user->id,
                    'aksi' => 'documentcombine',
                ]);
            } elseif ($request->input('condition2') == "feedback") {
                $isManager = strpos($user->rule, "Manager") !== false;
                $pesan = 'Memo: ' . $document->id . ' menerima feedback oleh ' . ($isManager ? 'Manager' : 'staff') . '.';
                $pesansingkat = 'Memo berhasil menerima feedback';

                if (!$isManager) {
                    $pesan = "📢 *Feedback Baru pada Memo Sekdiv!* 📢\n"
                        . "📄 *Nama Memo Sekdiv:* {$document->documentname}\n"
                        . "📄 *No Memo Sekdiv:* {$document->documentnumber}\n"
                        . "🗣️ Feedback diberikan oleh " . $user->name . "*\n\n"
                        . "⚠️ *Aksi Diperlukan:* Mohon *Manager* untuk meninjau dan memberikan keputusan (✅ Approve / ❌ Reject) terkait feedback ini.\n\n"
                        . "📅 Tinjau segera untuk kelancaran proses! 🚀";

                    if ($user->rule == "MTPR") {
                    } else {
                        TelegramService::ujisendunit($user->rule, $pesan);
                    }
                }

                $document->systemLogs()->create([
                    'message' => json_encode([
                        'message' => $pesan,
                        'datasebelum' => '',
                        'datasesudah' => $document,
                    ]),
                    'level' => 'info',
                    'user' => $user->name,
                    'user_id' => $user->id,
                    'aksi' => 'documentfeedback',
                ]);
            }

            DB::commit(); // <-- Commit transaksi jika semua berhasil
            return redirect()->route('memosekdivs.show', ['id' => $document->id])->with('success', 'Dokumen berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack(); // <-- Rollback jika ada error
            return json_encode([
                'error' => 'Gagal memperbarui dokumen: ' . $e->getMessage()
            ]);
        }
    }
    public function sendDecision(Request $request, $id)
    {
        DB::beginTransaction(); // <-- Mulai transaksi
        try {
            $document = MemoSekdiv::findOrFail($id);
            $posisiid = $request->input('posisi');
            $decision = $request->input('decision');
            $newmemofeedback = MemoSekdivFeedback::findOrFail($posisiid);
            $newmemofeedback->condition1 = $decision;
            $newmemofeedback->save();



            $user = auth()->user();
            $isManager = strpos($user->rule, "Manager") !== false;

            if ($isManager) {
                try {
                    // Validasi ulang dokumen yang sama
                    $documentforcheckingmanager = MemoSekdiv::with(['feedbacks.files', 'timelines'])->findOrFail($id);
                    $documentforcheckingmanager = $document->detailonedocument();

                    $pesanAwal = "📢 *Manager sukses menyelesaikan keputusan!* 📢\n"
                        . "📄 *Nama Memo Sekdiv:* {$documentforcheckingmanager->documentname}\n"
                        . "📄 *No Memo Sekdiv:* {$documentforcheckingmanager->documentnumber}\n"
                        . "🗣️ Feedback diberikan oleh *{$user->name}*\n\n";

                    if ($user->unit->name == "MTPR") {
                    } else {
                        TelegramService::ujisendunit($user->unit->name, $pesanAwal);
                    }


                    // Jika unit terakhir sesuai dan belum ada last unit send
                    if (
                        $documentforcheckingmanager->unitlaststep === $user->unit->name &&
                        $documentforcheckingmanager->unitvalidation === "Aktif"
                    ) {
                        $decision = "Terkirim";
                        $condition2 = "";
                        $level = 'MTPR';
                        $documentname = $documentforcheckingmanager->documentname;
                        $idfeedback = $posisiid;
                        $hasil = $this->sendfowardDocumentlogic($id, $level, $idfeedback, $decision, $documentname, $condition2);

                        $pesanFinal = "📢 *Manager telah mengirim feedback ke Sekdiv!* 📢\n"
                            . "📄 *Nama Memo Sekdiv:* {$documentforcheckingmanager->documentname}\n"
                            . "📄 *No Memo Sekdiv:* {$documentforcheckingmanager->documentnumber}\n"
                            . "🗣️ Feedback diberikan oleh *{$user->name}*\n\n"
                            . "📅 Feedback sukses dikirim ke SM {$documentforcheckingmanager->SMname}! 🚀";


                        if ($user->unit->name == "MTPR") {
                        } else {
                            TelegramService::ujisendunit($user->unit->name, $pesanFinal);
                        }
                    }
                } catch (\Exception $e) {
                    return back()->withErrors(['error' => 'Gagal mengirim feedback: ' . $e->getMessage()]);
                }
            }
            DB::commit(); // <-- Commit transaksi jika berhasil
            return redirect()->route('memosekdivs.show', ['id' => $document->id])->with('success', 'Dokumen berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack(); // <-- Rollback jika terjadi error
            return back()->withErrors(['error' => 'Gagal mengirim feedback: ' . $e->getMessage()]);
        }
    }

    public function sendfowardDocumentlogic($id, $level, $idfeedback, $decision, $documentname, $conditionoffile2)
    {
        $document = MemoSekdiv::with('feedbacks')->findOrFail($id);
        $useronly =  auth()->user();
        $newmemofeedback = MemoSekdivFeedback::findOrFail($idfeedback);
        $newmemofeedback->condition1 = "Terkirim";
        $newmemofeedback->save();
        $files = $newmemofeedback->files;

        // Create new feedback
        $newmemofeedbackbaru = MemoSekdivFeedback::create([
            'memo_sekdiv_id'  => $id,
            'pic' =>  $useronly->rule ?? 'unknown',
            'author' => $newmemofeedback->author,
            'isread' => $newmemofeedback->isread,
            'reviewresult' => $newmemofeedback->reviewresult,
            'level' => $level,
            'email' => $newmemofeedback->email,
            'comment' => $newmemofeedback->comment,
            'condition1' => "Diterima",
            'condition2' => $conditionoffile2,
        ]);

        // Mengelola file
        foreach ($files as $file) {
            CollectFile::create([
                'filename' => $file->filename,
                'link' => $file->link,
                'collectable_id' => $newmemofeedbackbaru->id,
                'collectable_type' => MemoSekdivFeedback::class,
            ]);
        }





        if ($level === "selesai") {
            $timelines = collect($document->timelines); // Menggunakan collect untuk $timelines
            $documentclosed = $timelines->firstWhere('infostatus', 'documentclosed');
            if (!$documentclosed) {
                MemoSekdivTimeline::create([
                    'memo_sekdiv_id' => $document->id,
                    'infostatus' => 'documentclosed',
                    'entertime' => now(),
                ]);
            }
            $document->update([
                'documentstatus' => "close",
            ]);

            $pesan = "Memo " . $documentname . " telah ditutup.";

            // log
            $document->systemLogs()->create([
                'message' => json_encode([
                    'message' => 'Memo berhasil dibuat',
                    'datasebelum' => '',
                    'datasesudah' => $document,
                ]),
                'level' => 'info',
                'user' =>  $useronly->name,
                'user_id' =>  $useronly->id, // Add user_id here
                'aksi' => 'documentclosed',
            ]);

            return "Sukses";
        } else {
            // Menyusun daftar file untuk di-download
            $feedbacks = $document->feedbacks;
            $list = '';

            if ($feedbacks->isNotEmpty()) {
                $files = $feedbacks->last()->files;

                foreach ($files as $file) {
                    $list .= "📄 *" . $file->filename . "* ➡️ 🔗 Downloadfile_" . $file->id . "\n";
                }
            }

            $pesan = "Memo Sekdiv " . $documentname . " dikirimkan ke " . $level . " untuk menunggu persetujuan.\n\n" .
                "📂 Berikut daftar dokumen yang bisa diunduh by WhatsApp:\n" .
                $list .
                "\n🚀 *Ayo segera dikerjakan!* Terima kasih atas kerjasamanya! 🙏😊";

            // If $level starts with "Manager ", remove that prefix
            if (Str::startsWith($level, 'Manager ')) {
                $level = Str::replaceFirst('Manager ', '', $level);
            }

            // mail
            $unit = Unit::where('name', $level)->first();

            $document->notifsystem()->create([
                'status' => 'unread',
                'idunit' => $unit->id,
                'infostatus' => 'User dont read this message',
                'notifarray' => json_encode(
                    [
                        'type' => 'order',
                        'message' => 'Order received'
                    ]
                ),
            ]);





            // masukan log
            $document->systemLogs()->create([
                'message' => json_encode([
                    'message' => 'Memo berhasil dibuat',
                    'datasebelum' => '',
                    'datasesudah' => $document,
                ]),
                'level' => 'info',
                'user' => auth()->user()->name,
                'user_id' => auth()->user()->id, // Add user_id here
                'aksi' => 'documentfowarded',
            ]);

            return "Sukses";
        }
    }
    public function sendfowardDocument(Request $request, $id)
    {
        $request->validate([
            'level' => 'required',
            'idfeedback' => 'nullable|exists:memosekdiv_feedbacks,id', // Asumsi tabel feedbacks
            'decision' => 'required|string',
            'documentname' => 'required|string',
            'condition2' => 'nullable|string',
        ]);
        try {
            DB::beginTransaction();

            // Panggil logika pengiriman dokumen
            $hasil = $this->sendfowardDocumentlogic(
                $id,
                $request->input('level'),
                $request->input('idfeedback'),
                $request->input('decision'),
                $request->input('documentname'),
                $request->input('condition2')
            );

            // Jika $hasil mengembalikan nilai yang menunjukkan kegagalan, throw exception
            if (!$hasil) {
                throw new \Exception('Gagal memproses pengiriman dokumen');
            }

            DB::commit();

            return redirect()->route('memosekdivs.show', ['id' => $id])
                ->with('success', 'Dokumen berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('memosekdivs.show', ['id' => $id])
                ->with('error', 'Gagal memperbarui dokumen: ' . $e->getMessage());
        }
    }
}
