<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Cache;
use App\Models\Newbom;
use App\Models\Newprogressreport;
use App\Models\Category;
use App\Imports\BomsImport;
use App\Models\Newbomkomat;
use App\Models\Newbomkomathistory;
use App\Models\Wagroupnumber;
use App\Services\TelegramService;
use App\Models\NewMemo;
use App\Models\KomatRequirement;
use App\Models\ProjectType;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\NewbomExport;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class NewBOMController extends Controller
{

    public function updateKomatStatus(Request $request)
    {
        // Validate input
        $request->validate([
            'id' => 'required|integer|exists:newbomkomats,id',
            'status' => 'required|string|max:255',
            'newbom_id' => 'required|integer|exists:newboms,id',
            'statuslama' => 'nullable|string|max:255'
        ]);

        try {
            DB::beginTransaction();

            // Find the Newbomkomat record
            $newbomkomat = Newbomkomat::where('id', $request->id)
                ->where('newbom_id', $request->newbom_id)
                ->first();

            if (!$newbomkomat) {
                return response()->json([
                    'status' => 'error',
                    'title' => 'Gagal!',
                    'message' => 'Data komat tidak ditemukan.'
                ], 404);
            }

            // Store the old data for history logging
            $oldData = [
                'kodematerial' => $newbomkomat->kodematerial,
                'material' => $newbomkomat->material,
                'status' => $newbomkomat->status,
                'rev' => $newbomkomat->rev
            ];

            // Update the status
            $newbomkomat->status = $request->status;
            $newbomkomat->save();

            // Log the change in Newbomkomathistory
            foreach ($newbomkomat->histories as $history) {
                $history->update([
                    'status' => $newbomkomat->status,
                ]);
            }

            // Log the action in system logs
            $newbom = Newbom::findOrFail($request->newbom_id);
            $newbom->systemLogs()->create([
                'message' => json_encode([
                    'message' => 'Status komat diperbarui',
                    'datasebelum' => $oldData,
                    'datasesudah' => [
                        'kodematerial' => $newbomkomat->kodematerial,
                        'material' => $newbomkomat->material,
                        'status' => $newbomkomat->status,
                        'rev' => $newbomkomat->rev
                    ],
                ]),
                'level' => 'info',
                'user' => auth()->user()->name,
                'user_id' => auth()->user()->id,
                'aksi' => 'statusupdate'
            ]);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'title' => 'Berhasil!',
                'message' => 'Status komat berhasil diperbarui.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating komat status: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'title' => 'Kesalahan!',
                'message' => 'Terjadi kesalahan saat memperbarui status: ' . $e->getMessage()
            ], 500);
        }
    }
    public function destroyNewbom($id)
    {
        try {
            DB::beginTransaction();

            $newbom = Newbom::findOrFail($id);

            // Detach all related Newbomkomat records and their Newprogressreport relationships
            foreach ($newbom->newbomkomats as $newbomkomat) {
                // Detach Newprogressreport relationships
                $newbomkomat->newprogressreports()->detach();

                // Log detachment
                $newbom->systemLogs()->create([
                    'message' => json_encode([
                        'message' => 'Relasi Newprogressreport dilepaskan sebelum penghapusan Newbomkomat',
                        'newbomkomat_id' => $newbomkomat->id,
                        'kodematerial' => $newbomkomat->kodematerial,
                    ]),
                    'level' => 'info',
                    'user' => auth()->user()->name,
                    'user_id' => auth()->user()->id,
                    'aksi' => 'relationshipdetach',
                ]);

                // Delete Newbomkomat
                $newbomkomat->delete();
            }

            // Log Newbom deletion
            $newbom->systemLogs()->create([
                'message' => json_encode([
                    'message' => 'Data Newbom dihapus',
                    'datasebelum' => $newbom->toArray(),
                    'datasesudah' => [],
                ]),
                'level' => 'info',
                'user' => auth()->user()->name,
                'user_id' => auth()->user()->id,
                'aksi' => 'newbomdelete',
            ]);

            // Delete the Newbom record
            $newbom->delete();

            DB::commit();

            return response()->json(['success' => true, 'message' => 'Newbom deleted successfully'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting Newbom: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'title' => 'Kesalahan!',
                'message' => 'Terjadi kesalahan saat menghapus Newbom: ' . $e->getMessage()
            ], 500);
        }
    }

    public function showNewbom($id)
    {
        // Fetch the specific newbom along with its related newbomkomats and systemLogs
        $newbom = Newbom::with([
            'newbomkomats.newprogressreports',
            'systemLogs' => function ($query) {
                $query->orderBy('created_at', 'desc'); // Order by the newest first
            }
        ])->findOrFail($id);

        // Fetch the project type related to the newbom with caching for 3 hours
        $project = Cache::remember("project_type_{$newbom->proyek_type_id}", 10800, function () use ($newbom) {
            return ProjectType::findOrFail($newbom->proyek_type_id);
        });

        // Fetch only the documents related to the project type of the specific newbom with caching
        $documents = Cache::remember('new_memos_with_related_data', 600, function () {
            return NewMemo::with(['feedbacks', 'komats', 'timelines'])->get();
        });

        // Get additional data for the fetched documents
        $additionalData = NewMemo::getAdditionalDataalldocumentdirect($documents);

        // Check if the additional data has the required keys
        if (!isset($additionalData['listdatadocuments']) || !isset($additionalData['percentagememoterbuka']) || !isset($additionalData['percentagememotertutup'])) {
            // Handle case where data is incomplete
            return response()->json(['error' => 'Data tidak lengkap'], 500);
        }

        $listdatadocumentencode = $additionalData['listdatadocuments'];
        $percentagememoterbuka = $additionalData['percentagememoterbuka'];
        $percentagememotertutup = $additionalData['percentagememotertutup'];

        // Generate BOM data
        [$groupedKomats, $groupprogress, $seniorpercentage, $materialopened, $materialclosed] = $newbom->bomoneshow($documents, [], $listdatadocumentencode);

        // Get the related newbomkomats
        $newbomkomats = $newbom->newbomkomats;

        $yourauth = auth()->user();
        $alldocumentrequirement = KomatRequirement::all();
        // Return the view with the necessary data
        return view('newbom.show', compact('newbom', 'newbomkomats', 'groupedKomats', 'groupprogress', 'seniorpercentage', 'yourauth', 'alldocumentrequirement'));
    }

    public function indexNewbom()
    {
        $projects = ProjectType::all(); // Fetch all projects for the dropdown
        $authuser = auth()->user();
        return view('newbom.index', compact('projects', 'authuser')); // Pass projects to the view
    }

    public function data(Request $request)
    {
        $query = Newbom::with('projectType'); // Eager load projectType relationship

        // Filter by project_type_id if provided
        if ($request->has('project_type_id') && $request->project_type_id != '') {
            $query->where('proyek_type_id', $request->project_type_id);
        }

        return DataTables::of($query)
            ->addIndexColumn() // Add index column for row numbering
            ->addColumn('checkbox', function ($newbom) {
                return '<input type="checkbox" value="' . $newbom->id . '" name="document_ids[]" id="checkbox' . $newbom->id . '">';
            })
            ->addColumn('project_type', function ($newbom) {
                return $newbom->projectType ? $newbom->projectType->title : '-';
            })
            ->addColumn('action', function ($newbom) {
                return '<a href="' . route('newbom.show', $newbom->id) . '" class="btn btn-primary btn-sm">Detail</a>';
            })
            ->rawColumns(['checkbox', 'action']) // Allow HTML in these columns
            ->make(true);
    }

    public function datashowrev($id)
    {
        try {
            $newbomkomat = Newbomkomat::with('histories')->findOrFail($id);
            $histories = $newbomkomat->histories->map(function ($history) {
                return [
                    'rev' => $history->rev,
                    'kodematerial' => $history->kodematerial,
                    'material' => $history->material,
                    'status' => $history->status,
                    'updated_at' => $history->updated_at->format('d-m-Y H:i:s'),
                ];
            });

            return response()->json([
                'success' => true,
                'histories' => $histories,
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching Newbomkomat history: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch revision history: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function indexlogpercentage()
    {
        $logs = Newbom::historyPercentage()->sortByDesc('created_at');
        return view('newbom.indexlogpercentage', compact('logs'));
    }
    
    public function deleteMultiple(Request $request)
    {
        try {
            $documentIds = $request->input('document_ids', []);
            if (empty($documentIds)) {
                return response()->json(['error' => 'No items selected for deletion.'], 400);
            }

            DB::beginTransaction();

            $newboms = Newbom::whereIn('id', $documentIds)->get();

            foreach ($newboms as $newbom) {
                // Detach and delete related Newbomkomat records
                foreach ($newbom->newbomkomats as $newbomkomat) {
                    // Detach Newprogressreport relationships
                    $newbomkomat->newprogressreports()->detach();

                    // Log detachment
                    $newbom->systemLogs()->create([
                        'message' => json_encode([
                            'message' => 'Relasi Newprogressreport dilepaskan sebelum penghapusan Newbomkomat',
                            'newbomkomat_id' => $newbomkomat->id,
                            'kodematerial' => $newbomkomat->kodematerial,
                        ]),
                        'level' => 'info',
                        'user' => auth()->user()->name,
                        'user_id' => auth()->user()->id,
                        'aksi' => 'relationshipdetach',
                    ]);

                    // Delete Newbomkomat
                    $newbomkomat->delete();
                }

                // Log Newbom deletion
                $newbom->systemLogs()->create([
                    'message' => json_encode([
                        'message' => 'Data Newbom dihapus',
                        'datasebelum' => $newbom->toArray(),
                        'datasesudah' => [],
                    ]),
                    'level' => 'info',
                    'user' => auth()->user()->name,
                    'user_id' => auth()->user()->id,
                    'aksi' => 'newbomdelete',
                ]);
            }

            // Delete the Newbom records
            Newbom::whereIn('id', $documentIds)->delete();

            DB::commit();

            return response()->json(['success' => true, 'message' => 'Selected items deleted successfully'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting multiple Newboms: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'title' => 'Kesalahan!',
                'message' => 'Terjadi kesalahan saat menghapus data: ' . $e->getMessage()
            ], 500);
        }
    }

    // Metode untuk Newbomkomat
    public function storeNewbomkomat(Request $request, $id)
    {
        // Create a new instance of Newbomkomat and assign request data to it
        $data = Newbom::infoall();
        $newbom = Newbom::findOrFail($id);
        $newbomkomat = new Newbomkomat();
        $newbomkomat->newbom_id = $id;
        $newbomkomat->kodematerial = $request->kodematerial;
        $newbomkomat->material = $request->material;
        $newbomkomat->status = $request->status;
        // Save the new instance to the database
        $newbomkomat->save();
        $newbom->systemLogs()->create([
            'message' => json_encode([
                'message' => 'Data dibuat',
                'datasebelum' => [],
                'datasesudah' => [$newbomkomat],
                'persentase' => $data['groupbomnumberpercentage'],
            ]),
            'level' => 'info',
            'user' => auth()->user()->name,
            'user_id' => auth()->user()->id, // Add user_id here
            'aksi' => 'bomcreate',
        ]);

        // Return the created resource as a JSON response with status code 201
        return response()->json($newbomkomat, 201);
    }

    public function changeNewbomkomat(Request $request, $id, $idkomat)
    {
        $data = Newbom::infoall();
        $newbom = Newbom::findOrFail($id);
        // Create a new instance of Newbomkomat and assign request data to it
        $newbomkomatsebelum = Newbomkomat::find($idkomat);
        $newbomkomat = Newbomkomat::find($idkomat);
        $newbomkomat->newbom_id = $id;
        $newbomkomat->kodematerial = $request->kodematerial;
        $newbomkomat->material = $request->material;
        $newbomkomat->status = $request->status;
        // Save the new instance to the database
        $newbomkomat->save();
        $newbom->systemLogs()->create([
            'message' => json_encode([
                'message' => 'Data diubah',
                'datasebelum' => [$newbomkomatsebelum],
                'datasesudah' => [$newbomkomat],
                'persentase' => $data['groupbomnumberpercentage'],
            ]),
            'level' => 'info',
            'user' => auth()->user()->name,
            'user_id' => auth()->user()->id, // Add user_id here
            'aksi' => 'bomchange',
        ]);

        // Return the created resource as a JSON response with status code 201
        return response()->json($newbomkomat, 201);
    }

    public function deleteNewbomkomat(Request $request, $id, $idkomat)
    {
        try {
            DB::beginTransaction();

            $data = Newbom::infoall();
            $newbomkomat = Newbomkomat::findOrFail($idkomat);
            $newbom = Newbom::findOrFail($id);

            // Detach all related Newprogressreport records
            $newbomkomat->newprogressreports()->detach();

            // Log the detachment action
            $newbom->systemLogs()->create([
                'message' => json_encode([
                    'message' => 'Relasi Newprogressreport dilepaskan sebelum penghapusan',
                    'newbomkomat_id' => $newbomkomat->id,
                    'kodematerial' => $newbomkomat->kodematerial,
                ]),
                'level' => 'info',
                'user' => auth()->user()->name,
                'user_id' => auth()->user()->id,
                'aksi' => 'relationshipdetach',
            ]);

            // Create a system log for deletion
            $newbom->systemLogs()->create([
                'message' => json_encode([
                    'message' => 'Data dihapus',
                    'datasebelum' => [$newbomkomat->toArray()],
                    'datasesudah' => [],
                    'persentase' => $data['groupbomnumberpercentage'],
                ]),
                'level' => 'info',
                'user' => auth()->user()->name,
                'user_id' => auth()->user()->id,
                'aksi' => 'bomdelete',
            ]);

            // Delete the Newbomkomat record
            $newbomkomat->delete();

            DB::commit();

            return response()->json(['success' => true, 'message' => 'Newbomkomat deleted successfully'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting Newbomkomat: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'title' => 'Kesalahan!',
                'message' => 'Terjadi kesalahan saat menghapus data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function importExcel(Request $request)
    {
        // Validate uploaded file
        $request->validate([
            'file' => 'required|mimes:xlsx,xls',
        ]);

        // Get the file from the request
        $file = $request->file('file');

        // Import data using BomsImport
        $import = new BomsImport();
        $revisiData = $import->collection(Excel::toCollection($import, $file)->first());

        $bom = Newbom::where('BOMnumber', $request->bomnumber)->first();

        if ($bom) {
            return response()->json(['Message' => "Sudah pernah diiput"]);
        } else {
            $newbom = Newbom::create([
                'BOMnumber' => $request->bomnumber,
                'proyek_type' => "",
                'proyek_type_id' => $request->project_type_id,
                'unit' => $request->unit,
            ]);

            $newbomKomatData = [];
            foreach ($revisiData as $data) {
                $newbomKomatData[] = [
                    'newbom_id' => $newbom->id,
                    'kodematerial' => $data['kodematerial'],
                    'material' => $data['material'],
                    'status' => $data['status'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            Newbomkomat::insert($newbomKomatData);
            $hasil = Newbom::infoall();
            $newbom->systemLogs()->create([
                'message' => json_encode([
                    'message' => 'Data ditambahkan',
                    'datasebelum' => [],
                    'datasesudah' => $newbomKomatData,
                    'persentase' => $hasil['groupbomnumberpercentage'],
                ]),
                'level' => 'info',
                'user' => auth()->user()->name,
                'user_id' => auth()->user()->id, // Add user_id here
                'aksi' => 'bomaddition',
            ]);
        }

        return redirect()->route('newbom.index');
    }

    public function showUploadForm()
    {
        $projects = ProjectType::all();
        $units = Category::getlistCategoryMemberByName('unitunderpe');
        return view('newbom.uploadbom', compact('projects', 'units'));
    }

    public function operatorfindbykomat(Request $request)
    {
        // Validate the request input
        $request->validate([
            'komat' => 'required|string|max:255',
        ]);

        // Retrieve the 'komat' value from the request
        $komat = $request->input('komat');

        // Find 'Newbomkomat' entity based on 'komat' value
        $newbomkomat = Newbomkomat::where('kodematerial', $komat)->first();

        // If not found, return an empty response with 404 status
        if (!$newbomkomat) {
            return response()->json(['operator' => ''], 404); // 404 Not Found
        }

        // Find related 'Newbom' entity using ID from 'Newbomkomat'
        $newbom = Newbom::find($newbomkomat->newbom_id);

        // Get 'unit' as operator or return an empty string if 'unit' does not exist
        $operator = $newbom->unit ?? '';

        // Return operator as JSON response
        return response()->json(['operator' => $operator]);
    }

    public function formatprogress(Request $request)
    {
        $validatedData = $request->validate([
            'file' => 'required|file|mimes:xlsx,xls',
        ]);

        $file = $request->file('file');
        $listproject = Cache::remember('list_project_titles', 180, function () {
            return ProjectType::pluck('title')->all();
        });

        $revisiData = Excel::toArray(new \stdClass(), $file)[0];
        if (empty($revisiData)) {
            return response()->json(['error' => 'No data found in the Excel file.'], 400);
        }

        // Process the imported data
        $processedData = $this->progressreportexported($revisiData);

        $groupedProcessedData = [];
        $allProjectTypes = ProjectType::pluck('id', 'title')->toArray();
        $newRecordsall = [];
        $updateRecordsall = [];
        $resumeEntries = []; // Array untuk menyimpan entri resume

        try {
            DB::beginTransaction();

            // Untuk menampung relasi yang akan di-sync di akhir
            $pendingRelations = [];

            foreach ($processedData as $item) {
                $proyek_type_title = trim($item['proyek_type']);
                $unit = $item['unit'];
                $bomnumber = $item['bomnumber'];

                if (empty($proyek_type_title) || empty($unit) || empty($bomnumber)) {
                    continue;
                }

                if (!isset($allProjectTypes[$proyek_type_title])) {
                    continue;
                }

                $proyek_type_id = $allProjectTypes[$proyek_type_title];
                $groupKey = $proyek_type_id . '@' . $unit . '@' . $bomnumber;

                if (!isset($groupedProcessedData[$groupKey])) {
                    $groupedProcessedData[$groupKey] = [];
                }

                $groupedProcessedData[$groupKey][] = $item;
            }

            foreach ($processedData as $key => $item) {
                if ($item["unit"] == null) {
                    unset($processedData[$key]);
                }
            }

            $exportedRecords = [];
            $exportedCount = 0;

            $allBomprogress = Newbom::whereIn('proyek_type_id', array_column($processedData, 'proyek_type_id'))
                ->get()
                ->keyBy(function ($item) {
                    return $item->proyek_type_id . '@' . $item->unit . '@' . $item->BOMnumber;
                });

            foreach ($groupedProcessedData as $groupKey => $data) {
                list($proyek_type_id, $unit, $bomnumber) = explode('@', $groupKey);

                $bomprogress = $allBomprogress->get($groupKey);
                if (!$bomprogress) {
                    $bomprogress = Newbom::firstOrCreate([
                        'proyek_type_id' => $proyek_type_id,
                        'unit' => $unit,
                        'BOMnumber' => $bomnumber
                    ]);
                }

                $id = $bomprogress->id;
                $kodematerialList = array_column($data, 'kodematerial');

                $allKodematRecords = Newbomkomat::with('newprogressreports')
                    ->where('newbom_id', $id)
                    ->whereIn('kodematerial', $kodematerialList)
                    ->get()
                    ->groupBy('kodematerial');

                $newRecords = [];
                $updateRecords = [];
                $historyRecords = [];

                foreach ($data as $item) {
                    $kodematerial = trim($item['kodematerial']);
                    $keterangan = $item['keterangan'];
                    $material = $item['material'];

                    if (empty($kodematerial)) {
                        continue;
                    }

                    if (strpos(strtolower($keterangan), 'delete') !== false) {
                        $existingRecord = optional($allKodematRecords->get($kodematerial))->first();
                        if ($existingRecord) {
                            // Lepaskan semua relasi progressreport dulu
                            $existingRecord->newprogressreports()->detach();

                            // Catat di log bahwa relasi dilepas
                            $bomprogress->systemLogs()->create([
                                'message' => json_encode([
                                    'message' => 'Relasi Newprogressreport dilepaskan sebelum penghapusan',
                                    'newbomkomat_id' => $existingRecord->id,
                                    'kodematerial' => $existingRecord->kodematerial,
                                ]),
                                'level' => 'info',
                                'user' => auth()->user()->name,
                                'user_id' => auth()->user()->id,
                                'aksi' => 'relationshipdetach',
                            ]);
                            $existingRecord->delete();
                            $bomprogress->systemLogs()->create([
                                'message' => json_encode([
                                    'message' => 'Material deleted',
                                    'kodematerial' => $kodematerial,
                                    'keterangan' => $keterangan,
                                ]),
                                'level' => 'info',
                                'user' => auth()->user()->name,
                                'user_id' => auth()->user()->id,
                                'aksi' => 'materialdeletion',
                            ]);
                        }
                        continue;
                    }

                    $rev = $item['rev'];
                    if ($rev === null || $rev === '') {
                        continue;
                    }

                    $kodematerialRecords = $allKodematRecords->get($kodematerial);

                    if ($kodematerialRecords && $kodematerialRecords->isNotEmpty()) {
                        $existingRecord = $kodematerialRecords->first();
                        if ($this->compareRevisions($rev, $existingRecord->rev) || $existingRecord->rev == null) {
                            $existingRecord->material = $item['material'];
                            $existingRecord->status = $item['status'] ?? $existingRecord->status;
                            $existingRecord->rev = $rev;

                            $historyRecords[] = [
                                'newbomkomat_id' => $existingRecord->id,
                                'kodematerial' => $kodematerial,
                                'material' => $item['material'],
                                // 'status' => $item['status'] ?? $existingRecord->status,
                                'rev' => $rev,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ];

                            $updateRecordsall[] = $existingRecord;
                            $updateRecords[] = $existingRecord;

                            $exportedRecords[] = $item;
                            $exportedCount++;
                        } else {
                            $exportedRecords[] = $item;
                            $exportedCount++;
                        }
                        // Kumpulkan relasi untuk proses akhir
                        // Tambahkan ini DI LUAR if-else, agar selalu mengumpulkan relasi jika ada
                        if (!empty($item['newprogressreport_relations'])) {
                            $pendingRelations[] = [
                                'type' => 'update',
                                'kodematerial' => $kodematerial,
                                'material' => $item['material'],
                                'rev' => $rev,
                                'relations' => $item['newprogressreport_relations'],
                                'newbom_id' => $id,
                            ];
                        }
                    } else {
                        $datanewrecords = [
                            'newbom_id' => $id,
                            'kodematerial' => $kodematerial,
                            'material' => $item['material'],
                            'rev' => $rev,
                            // 'status' => $item['status'] ?? '',
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                        $newRecords[] = $datanewrecords;
                        $newRecordsall[] = $datanewrecords;

                        $exportedRecords[] = $item;
                        $exportedCount++;

                        // Kumpulkan relasi untuk proses akhir (insert)
                        if (!empty($item['newprogressreport_relations'])) {
                            $pendingRelations[] = [
                                'type' => 'insert',
                                'kodematerial' => $kodematerial,
                                'material' => $item['material'],
                                'rev' => $rev,
                                'relations' => $item['newprogressreport_relations'],
                                'newbom_id' => $id,
                            ];
                        }
                    }
                }

                // Insert new records
                $newlyInsertedRecords = collect();
                if (!empty($newRecords)) {
                    foreach ($newRecords as $record) {
                        try {
                            $newbomkomat = Newbomkomat::updateOrCreate(
                                [
                                    'newbom_id' => $record['newbom_id'],
                                    'kodematerial' => $record['kodematerial'],
                                ],
                                [
                                    'material' => $record['material'],
                                    'rev' => $record['rev'],
                                    // 'status' => $record['status'] ?? '',
                                    'created_at' => $record['created_at'] ?? now(),
                                    'updated_at' => $record['updated_at'] ?? now(),
                                ]
                            );
                            $key = $record['material'] . '@' . $record['kodematerial'];
                            $newlyInsertedRecords[$key] = $newbomkomat;
                        } catch (\Illuminate\Database\QueryException $e) {
                            if ($e->getCode() == 23000) {
                                Log::warning('Duplicate entry attempted for Newbomkomat', [
                                    'newbom_id' => $record['newbom_id'],
                                    'kodematerial' => $record['kodematerial'],
                                ]);
                                continue;
                            }
                            throw $e;
                        }
                    }
                    // Update historyRecords with newbomkomat_id
                    foreach ($historyRecords as &$history) {
                        $key = $history['material'] . '@' . $history['kodematerial'];
                        if (isset($newlyInsertedRecords[$key])) {
                            $history['newbomkomat_id'] = $newlyInsertedRecords[$key]->id;
                        }
                    }
                }

                // Update records
                if (!empty($updateRecords)) {
                    foreach ($updateRecords as $record) {
                        $record->save();
                    }
                }

                // Insert history
                if (!empty($historyRecords)) {
                    Newbomkomathistory::insert($historyRecords);
                }

                // Proses relasi di akhir
                foreach ($pendingRelations as $rel) {
                    $record = Newbomkomat::where('kodematerial', $rel['kodematerial'])
                        ->where('newbom_id', $rel['newbom_id'])
                        ->first();
                    if ($record) {
                        $newprogressreport_ids = array_column($rel['relations'], 'newProgressReportId');
                        if (!empty($newprogressreport_ids)) {
                            $record->newprogressreports()->sync($newprogressreport_ids);

                            // Generate resume entry
                            $relasiDetails = '';
                            foreach ($rel['relations'] as $relation) {
                                $relasiDetails .= "\t📄 Nomor Dokumen: {$relation['newProgressReportNumber']}\n";
                                $relasiDetails .= "\t🔁 Revisi Dokumen: {$relation['historyRev']}\n";
                            }

                            $resumeEntries[] = "📦 Kode Material: {$rel['kodematerial']}\n"
                                . "📑 Material: {$rel['material']}\n"
                                . "🔧 Rev Komat: {$rel['rev']}\n"
                                . "📊 Relasi Progress:\n$relasiDetails";

                            // Log the attachment
                            $bomprogress->systemLogs()->create([
                                'message' => json_encode([
                                    'message' => 'Progress reports attached',
                                    'kodematerial' => $rel['kodematerial'],
                                    'newprogressreport_ids' => $newprogressreport_ids,
                                ]),
                                'level' => 'info',
                                'user' => auth()->user()->name,
                                'user_id' => auth()->user()->id,
                                'aksi' => 'progressreportattachment',
                            ]);
                        }
                    }
                }

                $bomprogress->systemLogs()->create([
                    'message' => json_encode(['message' => 'Materials processed', 'count' => count($historyRecords)]),
                    'level' => 'info',
                    'user' => auth()->user()->name,
                    'user_id' => auth()->user()->id,
                    'aksi' => 'materialprocessing',
                ]);
            }

            // Commit the transaction

            // Buat string resume menggunakan .= dengan emoticon
            $resume = "📋 Laporan Kode Material 📋\n\n";
            if (!empty($resumeEntries)) {
                foreach ($resumeEntries as $entry) {
                    $resume .= $entry . "\n";
                }
            } else {
                $resume .= "⚠️ Tidak ada data material yang diproses.\n";
            }
            $resume .= "✅ Proses selesai!";
            DB::commit();

            // Kirim resume melalui WhatsApp
            TelegramService::ujisendunit("Sinkron SAP", $resume);

            // Returning only successfully inserted or updated records
            return response()->json([
                'message' => 'Materials processed successfully',
                'data' => [
                    'newRecords' => $newRecordsall,
                    'updatedRecords' => $updateRecordsall,
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


    public function progressreportexported($importedData)
    {
        $revisiData = [];
        $allValidCodes = [];
        $projectTypeMap = ProjectType::pluck('id', 'title')->mapWithKeys(function ($id, $title) {
            return [trim($title) => $id];
        });

        // Define valid revisions
        $validSingleRevisions = array_merge(['0'], range('A', 'Z'));
        $validDoubleRevisions = [];
        foreach (range('A', 'Z') as $first) {
            foreach (range('A', 'Z') as $second) {
                $validDoubleRevisions[] = $first . $second;
            }
        }

        try {
            $tempData = [];

            foreach ($importedData as $row) {
                $proyek_type = trim($row[1] ?? "");
                $proyek_type_id = $projectTypeMap[$proyek_type] ?? null;
                $unit = $this->perpanjangan(trim($row[2] ?? ""));
                $bomnumber = trim($row[3] ?? "");
                $rev = (string) trim($row[4] ?? "");
                $kodematerial = trim($row[5] ?? "");

                if (strpos($kodematerial, "\n") !== false || strpos($kodematerial, " ") !== false) {
                    $parts = preg_split('/\r\n|\r|\n| /', $kodematerial);
                    $kodematerial = trim(end($parts));
                }

                $material = trim($row[6] ?? "");
                $deskripsi = trim($row[7] ?? "");
                $status = trim($row[8] ?? "");
                $keterangan = trim($row[9] ?? "");


                [$allValidCodes, $validCodes] = $this->extractvalidcode($allValidCodes, $deskripsi);
                // Simpan data sementara
                $tempData[] = [
                    'proyek_type' => $proyek_type,
                    'unit' => $unit,
                    'bomnumber' => $bomnumber,
                    'rev' => $rev,
                    'kodematerial' => $kodematerial,
                    'material' => $material,
                    'status' => $status,
                    'keterangan' => $keterangan,
                    'validCodes' => $validCodes,
                ];
            }


            // // Langkah 2: Lakukan satu query untuk semua validCodes
            $allValidCodes = array_unique($allValidCodes);
            $newProgressReports = collect();

            if (!empty($allValidCodes)) {
                $newProgressReports = Newprogressreport::with([
                    'latestHistory',
                    'newreport.projectType'
                ])
                    ->whereIn('nodokumen', $allValidCodes)
                    ->get()
                    ->keyBy(function ($item) {
                        // Pastikan semua relasi tidak null untuk menghindari error
                        $projectTypeTitle = optional(optional($item->newreport)->projectType)->title;
                        $unitTitle = optional($item->newreport)->unit;

                        return $item->nodokumen . '-' . $projectTypeTitle . '-' . $unitTitle;
                    });
            }


            // // Langkah 3: Proses ulang tempData untuk membangun revisiData
            foreach ($tempData as $data) {
                if (empty($data['kodematerial'])) {
                    continue; // Skip jika kodematerial kosong
                }

                if (strpos(strtolower($data['keterangan']), 'delete') !== false) {
                    $data['keterangan'] = 'delete';
                }

                if ($data['proyek_type'] !== "" && $data['unit'] !== "" && $data['bomnumber'] !== "" && $data['kodematerial'] !== "") {
                    $newProgressReportRelations = [];

                    foreach ($data['validCodes'] as $code) {
                        $key = $code . "-" . $data['proyek_type'] . "-" . $data['unit'];
                        if (isset($newProgressReports[$key])) {
                            $report = $newProgressReports[$key];

                            // Cek apakah newprogressreporthistory tidak kosong
                            $historyRev = optional($report->latestHistory)->rev ?? "Cek di Vault";
                            $relasi = [
                                'newProgressReportId' => $report->id,
                                'newProgressReportNumber' => $code,
                                'historyRev' => $historyRev,
                            ];

                            $newProgressReportRelations[] = $relasi;
                        }
                    }

                    $revisiData[] = [
                        'proyek_type' => $data['proyek_type'],
                        'unit' => $data['unit'],
                        'bomnumber' => $data['bomnumber'],
                        'rev' => $data['rev'],
                        'kodematerial' => $data['kodematerial'],
                        'material' => $data['material'],
                        'status' => $data['status'],
                        'keterangan' => $data['keterangan'],
                        'newprogressreport_relations' => $newProgressReportRelations,
                    ];
                }
            }
        } catch (\Exception $e) {
            Log::error('Error processing data in progressreportexported: ' . $e->getMessage());
            return response()->json(['error' => 'Error processing data: ' . $e->getMessage()], 500);
        }

        return $revisiData;
    }

    private function extractvalidcode($allValidCodes, $deskripsi)
    {
        // Process deskripsi untuk valid codes
        $validCodes = [];
        if (!empty($deskripsi)) {
            if (is_object($deskripsi) && $deskripsi instanceof \PhpOffice\PhpSpreadsheet\RichText\RichText) {
                foreach ($deskripsi->getRichTextElements() as $element) {
                    if ($element->getFont() && $element->getFont()->getStrikethrough()) {
                        continue;
                    }
                    $elementText = $element->getText();
                    $words = preg_split('/[\s,]+/', $elementText);
                    foreach ($words as $word) {
                        if (str_contains($word, '.') && str_contains($word, '-')) {
                            $cleanWord = str_replace(['(', ')'], '', trim($word));
                            if (preg_match('/^[\w\.-]+$/', $cleanWord)) {
                                $validCodes[] = $cleanWord;
                                $allValidCodes[] = $cleanWord;
                            }
                        }
                    }
                }
            } else {
                $words = preg_split('/[\s,]+/', $deskripsi);
                foreach ($words as $word) {
                    if (str_contains($word, '.') && str_contains($word, '-')) {
                        $cleanWord = str_replace(['(', ')'], '', trim($word));
                        if (preg_match('/^[\w\.-]+$/', $cleanWord)) {
                            $validCodes[] = $cleanWord;
                            $allValidCodes[] = $cleanWord;
                        }
                    }
                }
            }
        }
        return [$allValidCodes, $validCodes];
    }

    private function compareRevisions($newRev, $oldRev)
    {
        // Define the single-letter revision order
        $singleLetterOrder = array_merge(['0'], range('A', 'Z'));

        // Function to get revision index
        $getRevisionIndex = function ($rev) use ($singleLetterOrder) {
            // Handle single-character revisions (0, A-Z)
            if (strlen($rev) === 1 && in_array($rev, $singleLetterOrder)) {
                return array_search($rev, $singleLetterOrder);
            }

            // Handle two-letter revisions (AA-AZ)
            if (strlen($rev) === 2 && ctype_alpha($rev) && ctype_upper($rev)) {
                $firstLetter = $rev[0];
                $secondLetter = $rev[1];
                if (in_array($firstLetter, range('A', 'Z')) && in_array($secondLetter, range('A', 'Z'))) {
                    // Map AA-AZ to indices after Z (27 = index of Z + 1, 28 = AA, 29 = AB, ..., 52 = AZ)
                    $baseIndex = array_search('Z', $singleLetterOrder) + 1; // 27
                    $secondLetterIndex = array_search($secondLetter, range('A', 'Z')); // 0 for A, 1 for B, ..., 25 for Z
                    return $baseIndex + $secondLetterIndex;
                }
            }

            // Return a high number for invalid revisions to prevent updates
            return PHP_INT_MAX;
        };

        $newRevIndex = $getRevisionIndex($newRev);
        $oldRevIndex = $getRevisionIndex($oldRev);

        // If either revision is invalid, do not update
        if ($newRevIndex === PHP_INT_MAX || $oldRevIndex === PHP_INT_MAX) {
            return false;
        }

        // Return true if new revision is newer (has a higher index) than the old one
        return $newRevIndex > $oldRevIndex;
    }


    public function formatupdateprogress(Request $request)
    {
        $validatedData = $request->validate([
            'file' => 'required|file|mimes:xlsx,xls',
        ]);

        try {
            // Mengambil file dari request
            $file = $request->file('file');

            // Membaca data dari file Excel
            $revisiData = Excel::toArray(new \stdClass(), $file)[0];

            if (empty($revisiData)) {
                return response()->json(['error' => 'No data found in the Excel file.'], 400);
            }

            // Memproses data yang diimpor
            $processedData = $this->updatekomatprogressexported($revisiData);

            // Mengumpulkan data yang diperlukan untuk batch query
            $bomnumbers = array_unique(array_column($processedData, 'bomnumber'));
            $kodematerials = array_unique(array_column($processedData, 'kodematerial'));

            // Query untuk semua Newbom berdasarkan BOMnumber
            $newboms = Newbom::whereIn('BOMnumber', $bomnumbers)->get()->keyBy('BOMnumber');

            // Query untuk semua Newbomkomat berdasarkan kodematerial dan newbom_id
            $newbomkomats = Newbomkomat::whereIn('kodematerial', $kodematerials)
                ->whereIn('newbom_id', $newboms->pluck('id')->toArray())
                ->get()
                ->keyBy(fn($komat) => $komat->kodematerial . '|' . $komat->newbom_id);

            // Mengumpulkan semua spesifikasi untuk batch query
            $allSpesifikasi = [];
            foreach ($processedData as $item) {
                $allSpesifikasi = array_merge($allSpesifikasi, $item['spesifikasi']);
            }

            $newprogressreports = Newprogressreport::whereIn('nodokumen', $allSpesifikasi)->get()->keyBy('nodokumen');

            // Memproses data
            foreach ($processedData as $item) {
                $kodematerial = trim($item['kodematerial']);
                $bomnumber = trim($item['bomnumber']);

                // Ambil data Newbom dan Newbomkomat dari koleksi
                $newbom = $newboms[$bomnumber] ?? null;
                if (!$newbom) {
                    continue; // Skip jika newbom tidak ditemukan
                }

                $newbomkomatKey = $kodematerial . '|' . $newbom->id;
                $newbomkomat = $newbomkomats[$newbomkomatKey] ?? null;
                if (!$newbomkomat) {
                    continue; // Skip jika newbomkomat tidak ditemukan
                }

                $idsnewprogressrepots = [];
                foreach ($item['spesifikasi'] as $spesifikasi) {
                    $newprogressreport = $newprogressreports[$spesifikasi] ?? null;
                    if ($newprogressreport) {
                        $idsnewprogressrepots[] = $newprogressreport->id;
                    }
                }

                // Attach IDs jika ada
                if (!empty($idsnewprogressrepots)) {
                    $newbomkomat->newprogressreports()->attach($idsnewprogressrepots);
                }
            }

            return response()->json(['success' => 'Data processed successfully.'], 200);
        } catch (\Exception $e) {
            Log::error('Error processing progress update: ' . $e->getMessage());
            return response()->json(['error' => 'Error processing data: ' . $e->getMessage()], 500);
        }
    }

    public function updatekomatprogressexported($importedData)
    {
        $revisiData = [];

        try {
            foreach ($importedData as $row) {
                // Validasi bahwa elemen array yang diperlukan tersedia
                $bomnumber = isset($row[1]) ? trim($row[1]) : "";
                $kodematerial = isset($row[3]) ? trim($row[3]) : "";
                $spesifikasi = isset($row[4]) ? explode(",", trim($row[4])) : [];

                // Pastikan data tidak kosong
                if (!empty($bomnumber) && !empty($kodematerial) && !empty($spesifikasi)) {
                    $revisiData[] = [
                        'bomnumber' => $bomnumber,
                        'kodematerial' => $kodematerial,
                        'spesifikasi' => $spesifikasi,
                    ];
                }
            }
        } catch (\Exception $e) {
            Log::error('Error processing data in progressreportexported: ' . $e->getMessage());
            return response()->json(['error' => 'Error processing data: ' . $e->getMessage()], 500);
        }

        return $revisiData;
    }

    public function importExcelsistem(Request $request)
    {
        $jenisupload = $request->jenisupload;

        if ($jenisupload == "formatprogress") {
            $hasil = $this->formatprogress($request);
        } elseif ($jenisupload == "formatupdateprogress") {
            $hasil = $this->formatupdateprogress($request);
        } elseif ($jenisupload == "formatrencana") {
            $hasil = $this->importExcel($request);
        }
        return $hasil;
    }

    public function perpanjangan($namasingkatan)
    {
        if ($namasingkatan == "QE") {
            return "Quality Engineering";
        } elseif ($namasingkatan == "EES") {
            return "Electrical Engineering System";
        } elseif ($namasingkatan == "MES") {
            return "Mechanical Engineering System";
        } elseif ($namasingkatan == "PE") {
            return "Product Engineering";
        } elseif ($namasingkatan == "EL") {
            return "Desain Elektrik";
        } elseif ($namasingkatan == "PS") {
            return "Preparation & Support";
        } elseif ($namasingkatan == "SD") {
            return "Shop Drawing";
        } elseif ($namasingkatan == "TP") {
            return "Teknologi Proses";
        } elseif ($namasingkatan == "WT") {
            return "Welding Technology";
        } elseif ($namasingkatan == "BG") {
            return "Desain Bogie & Wagon";
        } elseif ($namasingkatan == "CB") {
            return "Desain Carbody";
        } elseif ($namasingkatan == "SM") {
            return "Sistem Mekanik";
        } elseif ($namasingkatan == "INT") {
            return "Desain Interior";
        }
    }

    public function search(Request $request)
    {
        // Validasi input pencarian
        $validatedData = $request->validate([
            'query' => 'required|string|min:1',
        ]);

        // Ambil query dari input form
        $query = $validatedData['query'];

        // Ambil data dari database dengan eager loading untuk relasi
        $results = Newbomkomat::with(['newbom.projectType'])
            ->where('material', 'LIKE', '%' . $query . '%')
            ->orWhere('kodematerial', 'LIKE', '%' . $query . '%')
            ->get();

        // Inisialisasi string untuk menampung hasil dalam bentuk teks
        $textResult = "";

        // Jika ada hasil pencarian, tambahkan header
        if ($results->count() > 0) {
            $textResult .= "🔍 *Hasil Pencarian untuk:* _" . $query . "_\n\n";

            // Looping melalui hasil pencarian dan susun dalam format teks
            foreach ($results as $result) {
                $unit = $result->newbom->unit ?? "Tidak Diketahui";
                $project = $result->newbom->projectType->title ?? "Tidak Diketahui";

                $textResult .= "📌 *Material*: " . ($result->material ?? '-') . "\n";
                $textResult .= "🔄 *Kode Material*: " . ($result->kodematerial ?? '-') . "\n";
                $textResult .= "🏢 *Unit*: " . $unit . "\n";
                $textResult .= "🗷️ *Project*: " . $project . "\n";
                $textResult .= "----------------------------------\n\n"; // Garis pemisah antar dokumen
            }
        } else {
            // Jika tidak ada hasil, tambahkan pesan "Tidak ada hasil"
            $textResult = "⚠️ Tidak ada dokumen yang ditemukan untuk pencarian: *" . $query . "*";
        }

        // Kembalikan hasil pencarian dalam bentuk teks
        return response($textResult)->header('Content-Type', 'text/plain');
    }

    public function searchkomat(Request $request)
    {
        $query = $request->input('query');
        $results = [];

        if ($query) {
            $results = Newbomkomat::with(['newbom.projectType'])
                ->where('material', 'LIKE', '%' . $query . '%')
                ->orWhere('kodematerial', 'LIKE', '%' . $query . '%')
                ->get();
        }

        return view('newbom.search_results', compact('results', 'query'));
    }

    public function downloadbom($id)
    {
        return Excel::download(new NewbomExport($id), 'newbom.xlsx');
    }

    public function addRequirement(Request $request)
    {
        $validated = $request->validate([
            'newbomkomat_id' => 'required|exists:newbomkomats,id',
            'komat_requirement_id' => 'required|exists:komat_requirement,id',
        ]);

        $newbomkomat = Newbomkomat::findOrFail($validated['newbomkomat_id']);
        $newbomkomat->requirements()->syncWithoutDetaching([$validated['komat_requirement_id']]);

        return response()->json(['success' => true, 'message' => 'Requirement berhasil ditambahkan']);
    }

    public function removeRequirement(Request $request)
    {
        $validated = $request->validate([
            'newbomkomat_id' => 'required|exists:newbomkomats,id',
            'komat_requirement_id' => 'required|exists:komat_requirement,id',
        ]);

        $newbomkomat = Newbomkomat::findOrFail($validated['newbomkomat_id']);
        $newbomkomat->requirements()->detach($validated['komat_requirement_id']);

        return response()->json(['success' => true, 'message' => 'Requirement berhasil dihapus']);
    }

    public function addNewRequirementType(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'newbomkomat_id' => 'required|exists:newbomkomats,id',
            ]);

            // Simpan jenis dokumen baru
            $newRequirement = KomatRequirement::create([
                'name' => $request->name,
            ]);

            // (Opsional) Langsung kaitkan dokumen baru dengan newbomkomat
            $newbomkomat = Newbomkomat::findOrFail($request->newbomkomat_id);
            $newbomkomat->requirements()->attach($newRequirement->id);

            return response()->json([
                'success' => true,
                'message' => 'Jenis dokumen baru berhasil ditambahkan dan dikaitkan.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan jenis dokumen: ' . $e->getMessage(),
            ], 500);
        }
    }
}
