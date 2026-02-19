<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Cache;
use Carbon\Carbon; // Import Carbon class
use App\Models\Category;
use App\Models\Newreport;
use App\Models\NewprogressreportUnit;
use Illuminate\Http\Request;
use App\Models\ProjectType;
use App\Models\Unit;
use App\Models\Newprogressreport;
use App\Models\Newprogressreporthistory;
use App\Models\NewProgressReportsLevel;
use App\Models\NewProgressReportDocumentKind;
use App\Models\DailyNotification;
use Illuminate\Routing\Controller;
use Maatwebsite\Excel\Facades\Excel;
use Exception;
use App\Imports\RawprogressreportsImport;

use App\Imports\ProgressreportsTreediagramImport;
use Illuminate\Support\Facades\Log;
use DateTime;
use App\Models\Wagroupnumber;
use App\Services\TelegramService;
use App\Models\NotifHarianUnit;
use Illuminate\Support\Facades\DB;
use App\Jobs\DownloadProgressPdfJob;



class NewprogressreportController extends Controller
{


    public function store(Request $request)
    {
        // Validasi data yang diterima dari formulir jika diperlukan

        // Simpan data baru ke dalam database
        $newProgressReport = new Newprogressreport();
        $newProgressReport->newreport_id = $request->input('newreport_id');
        $newProgressReport->nodokumen = $request->input('nodokumen');
        $newProgressReport->namadokumen = $request->input('namadokumen');
        $newProgressReport->documentkind_id = $request->input('jenisdokumen');

        // Simpan progress report ke dalam database
        $newProgressReport->save();
        $newreportId = $newProgressReport->newreport_id;
        $newreport = Newreport::find($newreportId);
        $projectandvalue = Newreport::calculatelastpercentage();
        $newreport->systemLogs()->create([
            'message' => json_encode([
                'message' => 'Data dibuat',
                'datasebelum' => [],
                'datasesudah' => [$newProgressReport],
                'persentase' => $projectandvalue[0],
                'persentase_internal' => $projectandvalue[1],
            ]),
            'level' => 'info',
            'user' => auth()->user()->name,
            'user_id' => auth()->user()->id, // Add user_id here
            'aksi' => 'progresscreate',
        ]);

        // Redirect ke halaman index dengan pesan sukses
        return redirect()->route('newreports.index')->with('success', 'New progress report created successfully');
    }

    public function destroy($id)
    {
        // Cari entitas berdasarkan id
        $newProgressReport = Newprogressreport::find($id);

        if (auth()->user()->name == "Dian Pertiwi") {
            // Pastikan entitas ditemukan sebelum dihapus
            if ($newProgressReport) {
                // Hapus entitas
                $progressReportsBeforeDelete = [$newProgressReport];
                $newreportId = $newProgressReport->newreport_id;
                $newProgressReport->delete();


                // Ambil model Newreport
                $newreport = Newreport::find($newreportId);
                $projectandvalue = Newreport::calculatelastpercentage();
                $newreport->systemLogs()->create([
                    'message' => json_encode([
                        'message' => 'Data dihapus',
                        'datasebelum' => $progressReportsBeforeDelete,
                        'datasesudah' => [],
                        'persentase' => $projectandvalue[0],
                        'persentase_internal' => $projectandvalue[1],
                    ]),
                    'level' => 'info',
                    'user' => auth()->user()->name,
                    'user_id' => auth()->user()->id, // Add user_id here
                    'aksi' => 'progressdelete',
                ]);

                // Redirect dengan pesan sukses jika berhasil
                return redirect()->route('newreports.index')->with('success', 'New progress report deleted successfully');
            } else {
                // Redirect dengan pesan error jika entitas tidak ditemukan
                return redirect()->route('newreports.index')->with('error', 'New progress report not found');
            }
        } else {
            // Pastikan entitas ditemukan sebelum dihapus
            if ($newProgressReport) {
                // Hapus entitas
                $progressReportsBeforeDelete = [$newProgressReport];
                $newreportId = $newProgressReport->newreport_id;
                if ($newProgressReport->status != "RELEASED") {
                    $newProgressReport->delete();
                }



                // Ambil model Newreport
                $newreport = Newreport::find($newreportId);
                $projectandvalue = Newreport::calculatelastpercentage();





                $newreport->systemLogs()->create([
                    'message' => json_encode([
                        'message' => 'Data dihapus',
                        'datasebelum' => $progressReportsBeforeDelete,
                        'datasesudah' => [],
                        'persentase' => $projectandvalue[0],
                        'persentase_internal' => $projectandvalue[1],
                    ]),
                    'level' => 'info',
                    'user' => auth()->user()->name,
                    'user_id' => auth()->user()->id, // Add user_id here
                    'aksi' => 'progressdelete',
                ]);

                // Redirect dengan pesan sukses jika berhasil
                return redirect()->route('newreports.index')->with('success', 'New progress report deleted successfully');
            } else {
                // Redirect dengan pesan error jika entitas tidak ditemukan
                return redirect()->route('newreports.index')->with('error', 'New progress report not found');
            }
        }
    }

    public function updateprogressreport(Request $request, $id)
    {
        try {
            $nodokumen = $request->input('nodokumen') ?? "";
            $namadokumen = $request->input('namadokumen') ?? "";
            $level = $request->input('level') ?? "";
            $drafter = $request->input('drafter') ?? "";
            $checker = $request->input('checker') ?? "";

            $progressnodokumen = $request->input('progressnodokumen') ?? "";
            if ($progressnodokumen != "") {
                $newprogress = Newprogressreport::where('nodokumen', $progressnodokumen)->first();
                $newprogress->parent_revision_id = $id;
                $newprogress->save();
            }
            $deadlinereleasedate = $request->input('deadlinerelease') ?? "";
            $realisasi = $request->input('realisasi') ?? "";
            $status = $request->input('status') ?? "";

            $progressreport = Newprogressreport::findOrFail($id);
            $progressreportsebelum = Newprogressreport::findOrFail($id);

            $nodokumensebelum = $progressreport->nodokumen;
            $namadokumensebelum = $progressreport->namadokumen;
            $levelsebelum = $progressreport->level ?? "";
            $draftersebelum = $progressreport->drafter ?? "";
            $checkersebelum = $progressreport->checker ?? "";
            $deadlinereleasesebelum = $progressreport->deadlinereleasedate ?? "";
            $realisasisebelum = $progressreport->realisasi ?? "";
            $statussebelum = $progressreport->status ?? "";

            $progressreport->nodokumen = $nodokumen;
            $progressreport->namadokumen = $namadokumen;
            $progressreport->level = $level;
            $progressreport->drafter = $drafter;
            $progressreport->checker = $checker;

            // Mengonversi deadlinerelease dari format 'dd-mm-yyyy' ke format timestamp
            $progressreport->deadlinereleasedate = $deadlinereleasedate ? Carbon::createFromFormat('d-m-Y', $deadlinereleasedate) : null;

            $progressreport->realisasi = $realisasi;
            $progressreport->status = $status;
            $progressreport->save();


            $pesan = 'Perubahan dokumen. No Dokumen: ' . $nodokumensebelum . ' -> ' . $nodokumen . ', Nama Dokumen: ' . $namadokumensebelum . ' -> ' . $namadokumen . ', Drafter: ' . $draftersebelum . ' -> ' . $drafter . ', Checker: ' . $checkersebelum . ' -> ' . $checker . ', Deadline Release: ' . $deadlinereleasesebelum . ' -> ' . $deadlinereleasedate . ', Realisasi: ' . $realisasisebelum . ' -> ' . $realisasi . ', Status: ' . $statussebelum . ' -> ' . $status;

            // Panggil fungsi untuk memperbarui log
            $newreport = Newreport::find($progressreport->newreport_id);

            $projectandvalue = Newreport::calculatelastpercentage();
            $newreport->systemLogs()->create([
                'message' => json_encode([
                    'message' => $pesan,
                    'datasebelum' => [$progressreportsebelum],
                    'datasesudah' => [$progressreport],
                    'persentase' => $projectandvalue[0],
                    'persentase_internal' => $projectandvalue[1],
                ]),
                'level' => 'info',
                'user' => auth()->user()->name,
                'user_id' => auth()->user()->id, // Add user_id here
                'aksi' => 'progresschange',
            ]);
            return response()->json(['success' => 'Data berhasil diperbarui']);
        } catch (Exception $e) {
            return response()->json(['error' => 'Gagal memperbarui data: ' . $e->getMessage()], 500);
        }
    }

    public function showUploadFormExcel()
    {
        return view('newreports.uploadexcel');
    }

    public function importExcelsistem(Request $request)
    {
        $jenisupload = $request->jenisupload;

        if ($jenisupload == "formatprogress") {
            $hasil = $this->formatprogress($request);
        } elseif ($jenisupload == "formatprogresskhusus") {
            $hasil = $this->formatprogresskhusus($request);
        } elseif ($jenisupload == "formatrencana") {
            $hasil = $this->formatrencana($request);
        } else if ($jenisupload == "formatupdatelink") {
            $hasil = $this->formatupdatelink($request);
        }
        return $hasil;
    }

    public function formatrencana(Request $request)
    {
        //format butuh perbaikan
        // Validate request
        $validatedData = $request->validate([
            'file' => 'required|file|mimes:xlsx,xls',
        ]);

        // Get the file from the request
        $file = $request->file('file');
        // Import data using RawprogressreportsImport
        $import = new RawprogressreportsImport();
        $revisiData = Excel::toCollection($import, $file)->first();

        if (empty($revisiData)) {
            return response()->json(['error' => 'No data found in the Excel file.'], 400);
        }

        // Process imported data
        $processedData = $this->rencanaexported($revisiData);

        // Initialize an array to store grouped data
        $groupedData = [];

        $listproject = Cache::remember('list_project_titles', 180, function () {
            return ProjectType::pluck('title')->all();
        });

        $now = now();
        // Initialize arrays to collect successfully exported records
        $exportedRecords = [];
        $exportedCount = 0;
        // Group data by 'proyek_type' and 'unit'


        try {
            $allUnit = NewprogressreportUnit::whereIn('name', array_unique(array_column($processedData, 'unit')))
                ->get()
                ->keyBy('name');
            foreach ($processedData as $nodokumen => $item) {
                if (!isset($item['proyek_type']) || !isset($item['unit'])) {
                    return response()->json(['error' => 'Invalid data format.'], 400);
                }
                // Collect successfully exported records
                $proyek_type = $item['proyek_type'];
                $unit = $item['unit'];

                // Create a key based on proyek_type and unit
                $groupKey = $proyek_type . '-' . $unit;

                if (in_array($proyek_type, $listproject)) {
                    // Check if the key exists, otherwise initialize an empty array
                    if (!isset($groupedData[$groupKey])) {
                        $groupedData[$groupKey] = [];
                    }

                    // Add item to the grouped data array
                    $groupedData[$groupKey][] = $item;
                }
            }

            foreach ($groupedData as $groupKey => $data) {
                // Pastikan groupKey memiliki format yang benar
                if (!str_contains($groupKey, '-')) {
                    return response()->json([
                        'error' => true,
                        'message' => "Invalid groupKey format",
                        'groupKey' => $groupKey
                    ], 400);
                }

                list($proyek_type, $unit) = explode('-', $groupKey, 2); // Limit hanya 2 bagian

                // Cek apakah ProjectType ditemukan
                $project = ProjectType::where('title', $proyek_type)->first();
                if (!$project) {
                    return response()->json([
                        'error' => true,
                        'message' => "ProjectType not found",
                        'proyek_type' => $proyek_type
                    ], 404);
                }

                $unitModel = $allUnit[$unit] ?? null;
                if (!$unitModel) {
                    continue;
                }
                // Buat atau update Newreport
                $progressreport = Newreport::firstOrCreate(
                    [
                        'proyek_type_id' => $project->id,
                        'unit_id' => $unitModel->id
                    ],
                    [
                        'unit' => $unit,
                        'status' => 'Terbuka'
                    ]
                );

                $id = $progressreport->id;

                // Pastikan ada key 'nodokumen' dalam data
                if (empty(array_column($data, 'nodokumen'))) {
                    return response()->json([
                        'error' => true,
                        'message' => "nodokumen missing or empty",
                        'data' => $data
                    ], 400);
                }

                // Ambil existing records berdasarkan newreport_id dan nodokumen
                $existingReports = Newprogressreport::where('newreport_id', $id)
                    ->whereIn('nodokumen', array_column($data, 'nodokumen'))
                    ->get()
                    ->keyBy('nodokumen');

                $newRecords = [];
                $updateRecords = [];
                $exportedRecords = [];
                $exportedCount = 0;
                $now = now();

                foreach ($data as $item) {
                    $nodokumen = $item['nodokumen'];
                    if (isset($existingReports[$nodokumen])) {
                        $existingReport = $existingReports[$nodokumen];
                        $updateRecords[] = $existingReport;
                        $exportedRecords[] = $item;
                        $exportedCount++;
                    } else {
                        $newRecords[] = [
                            'newreport_id' => $id,
                            'nodokumen' => $nodokumen,
                            'namadokumen' => $item['namadokumen'] ?? "",
                            'created_at' => $now,
                            'updated_at' => $now,
                        ];
                        $exportedRecords[] = $item;
                        $exportedCount++;
                    }
                }

                // Bulk insert new records jika ada
                if (!empty($newRecords)) {
                    Newprogressreport::insert($newRecords);
                }

                // Bulk update existing records jika ada
                if (!empty($updateRecords)) {
                    foreach ($updateRecords as $record) {
                        if ($record) {
                            $record->save();
                        }
                    }
                }

                // Kalkulasi persentase terakhir
                try {
                    $projectandvalue = Newreport::calculatelastpercentage();
                } catch (Exception $e) {
                    return response()->json([
                        'error' => true,
                        'message' => "Error calculating last percentage",
                        'exception' => $e->getMessage()
                    ], 500);
                }

                // Simpan log sistem
                try {
                    $progressreport->systemLogs()->create([
                        'message' => json_encode([
                            'message' => 'Data Excel successfully imported',
                            'updatedata' => $updateRecords,
                            'databaru' => $newRecords,
                            'persentase' => $projectandvalue[0] ?? null,
                            'persentase_internal' => $projectandvalue[1] ?? null,
                        ]),
                        'level' => 'info',
                        'user' => auth()->user()->name,
                        'user_id' => auth()->user()->id,
                        'aksi' => 'progressaddition',
                    ]);
                } catch (Exception $e) {
                    return response()->json([
                        'error' => true,
                        'message' => "Error saving system log",
                        'exception' => $e->getMessage()
                    ], 500);
                }
            }

            // Return sukses jika semua berhasil
            return response()->json([
                'message' => 'Data Excel successfully imported',
                'exported_records' => $exportedRecords,
                'exported_count' => $exportedCount
            ], 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Error importing Excel file: ' . $e->getMessage()], 500);
        }
    }


    public function rencanaexported($importedData)
    {
        $revisiData = [];
        foreach ($importedData as $key => $row) {
            $unit = trim($row[1] ?? "");
            $proyek_type = trim($row[2] ?? "");
            $nodokumen = trim($row[3] ?? "");
            $namadokumen = trim($row[4] ?? "");

            // Check if any value is "-" or an empty string
            if (
                $unit === "-" || $proyek_type === "-" || $nodokumen === "-" ||
                $unit === "" || $proyek_type === "" || $nodokumen === ""
            ) {
                continue; // Skip this row
            }

            $revisiData[$nodokumen] = [
                'unit' => $unit,
                'proyek_type' => $proyek_type,
                'nodokumen' => $nodokumen,
                'namadokumen' => $namadokumen,
            ];
        }
        return $revisiData;
    }



    public function getvalueexcel($importedData)
    {
        foreach ($importedData as $key => $row) {
            if ($key === 0) {
                continue; // Skip the header row
            }

            $nowGeneration = null;
            $parent = '';

            for ($i = 0; $i < 8; $i++) {
                if (!empty(trim($row[$i] ?? ''))) {
                    $nowGeneration = trim($row[$i]);
                    $penyimpananIndukSementara[$i] = $nowGeneration;

                    if ($i === 0) {
                        $parent = "";
                    } else {
                        $parent = isset($penyimpananIndukSementara[$i - 1]) ? $penyimpananIndukSementara[$i - 1] : '';
                    }

                    $revisiData[$nowGeneration] = [
                        'noindukdokumen' => $parent,
                        'nodokumen' => $nowGeneration,
                    ];
                }
            }
        }
        return $revisiData;
    }

    private function transformDate($value)
    {
        if (is_numeric($value)) {
            $date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value);
            return $date->format('d-m-Y');
        }

        return $value;
    }
    // app/Http/Controllers/NewprogressreportController.php
    public function assignUnit(Request $request, NewProgressReportDocumentKind $kind)
    {
        $validated = $request->validate([
            'unit_id' => 'required|exists:newprogressreport_units,id'
        ]);

        $kind->update($validated);

        return back()->with('success', 'Unit berhasil dihubungkan ke Jenis Dokumen.');
    }

    public function updateDocumentKind(Request $request)
    {


        $progressReport = Newprogressreport::with('newprogressreporthistory')->findOrFail($request->progressreport_id);
        $progressReport->documentkind_id = $request->documentkind_id;
        $progressReport->save();
        // Update semua history terkait
        foreach ($progressReport->newprogressreporthistory as $history) {
            $history->documentkind_id = $request->documentkind_id;
            $history->save();
        }

        return response()->json([
            'success' => true,
            'documentkind_name' => $progressReport->documentKind->name ?? '',
            'message' => 'Jenis dokumen berhasil diperbarui!'
        ]);
    }



    public function unclearpdfdownload()
    {
        $undownloaded = Newprogressreporthistory::whereNotNull('fileid')
            ->where('isdownloaded', false)
            ->get();

        foreach ($undownloaded as $item) {
            dispatch(new DownloadProgressPdfJob(
                $item->fileid,
                Newprogressreporthistory::class,
                $item->id
            ));
        }

        return response()->json(['message' => 'PDF download jobs dispatched successfully (tanpa batch).']);
    }


    // Format progress report
    public function formatprogress(Request $request)
    {
        Log::info('Starting formatprogress function', ['user_id' => auth()->user()->id]);

        $validatedData = $request->validate([
            'file' => 'required|file|mimes:xlsx,xls',
        ]);
        $exportedCount = 0;

        $file = $request->file('file');
        Log::info('File received', ['file_name' => $file->getClientOriginalName()]);

        $listproject = Cache::remember('list_project_titles', 180, function () {
            return ProjectType::pluck('title')->all();
        });
        Log::info('Project types cached', ['listproject' => $listproject]);

        DB::beginTransaction();
        try {
            $import = new RawprogressreportsImport();
            $revisiData = Excel::toCollection($import, $file)->first();
            if (empty($revisiData)) {
                Log::error('No data found in Excel file');
                return response()->json(['error' => 'No data found in the Excel file.'], 400);
            }
            Log::info('Excel data imported', ['row_count' => $revisiData->count()]);

            $processedData = $this->progressreportexported($revisiData);
            Log::info('Processed data from progressreportexported', ['processed_data' => $processedData]);

            $groupedData = [];
            foreach ($processedData as $item) {
                $proyek_type = trim($item['proyek_type']);
                $unit = $item['unit'];
                $groupKey = $proyek_type . '-' . $unit;

                if (in_array($proyek_type, $listproject)) {
                    if (!isset($groupedData[$groupKey])) {
                        $groupedData[$groupKey] = [];
                    }
                    $groupedData[$groupKey][] = $item;
                }
            }
            Log::info('Grouped data by proyek_type and unit', ['grouped_data_keys' => array_keys($groupedData)]);

            $nodokumenList = array_map('strtoupper', array_column($processedData, 'nodokumen')); // Standardize to uppercase
            Log::info('Nodokumen list extracted', ['nodokumen_list' => $nodokumenList]);

            $allProjectTypes = ProjectType::whereIn('title', array_unique(array_column($processedData, 'proyek_type')))
                ->get()
                ->keyBy('title');
            $allUnit = NewprogressreportUnit::whereIn('name', array_unique(array_column($processedData, 'unit')))
                ->get()
                ->keyBy('name');
            $allDocumentKinds = NewProgressReportDocumentKind::whereIn('name', array_unique(array_column($processedData, 'jenisdokumen')))
                ->get()
                ->keyBy('name');
            Log::info('Database lookups completed', [
                'project_types' => $allProjectTypes->keys()->all(),
                'units' => $allUnit->keys()->all(),
                'document_kinds' => $allDocumentKinds->keys()->all()
            ]);

            $stringkiriman = "";
            foreach ($groupedData as $groupKey => $data) {
                $stringkiriman .= $groupKey . " ";
                list($proyek_type, $unit) = explode('-', $groupKey);
                $project = $allProjectTypes[$proyek_type] ?? null;
                if (!$project) {
                    Log::warning('Project type not found', ['proyek_type' => $proyek_type]);
                    continue;
                }
                $unitModel = $allUnit[$unit] ?? null;
                if (!$unitModel) {
                    Log::warning('Unit not found', ['unit' => $unit]);
                    continue;
                }

                $newreport = Newreport::firstOrCreate(
                    [
                        'proyek_type_id' => $project->id,
                        'unit_id' => $unitModel->id
                    ],
                    [
                        'unit' => $unit,
                        'status' => 'Terbuka'
                    ]
                );
                Log::info('Newreport created or retrieved', ['newreport_id' => $newreport->id, 'proyek_type' => $proyek_type, 'unit' => $unit]);

                $newreport_id = $newreport->id;

                $existingReportgroups = Newprogressreport::with(['newreport.projectType'])
                    ->whereIn('nodokumen', $nodokumenList)
                    ->get()
                    ->groupBy(function ($report) {
                        return strtoupper($report->nodokumen) . '-' . ($report->newreport->projectType->title ?? 'Unknown'); // Standardize to uppercase
                    });
                Log::info('Existing report groups', ['existing_report_groups' => $existingReportgroups->keys()->all()]);

                $nodokumenCounts = collect($nodokumenList)->mapWithKeys(function ($nodokumen) use ($existingReportgroups) {
                    $keys = $existingReportgroups->keys()->filter(function ($key) use ($nodokumen) {
                        return str_starts_with($key, $nodokumen . '-');
                    });
                    $values = [];
                    $keys->each(function ($key, $index) use (&$values) {
                        $values[$key] = $index === 0 ? 0 : 1;
                    });
                    return $values;
                })->toArray();
                Log::info('Nodokumen counts calculated', ['nodokumen_counts' => $nodokumenCounts]);

                $existingReports = Newprogressreport::where('newreport_id', $newreport_id)
                    ->whereIn('nodokumen', $nodokumenList)
                    ->get()
                    ->keyBy(function ($report) {
                        return strtoupper($report->nodokumen) . '-' . $report->newreport_id; // Standardize to uppercase
                    });
                Log::info('Existing reports for newreport_id', [
                    'newreport_id' => $newreport_id,
                    'existing_reports' => $existingReports->keys()->all()
                ]);

                $existingNodokumenInDB = $existingReports->keys()->all();

                $existingHistory = Newprogressreporthistory::with(['newProgressReport.newreport.projectType'])
                    ->whereIn('nodokumen', $nodokumenList)
                    ->whereIn('rev', array_column($data, 'rev'))
                    ->get()
                    ->keyBy(function ($item) {
                        return strtoupper($item->nodokumen) . '-' . $item->rev . '-' . $item->newProgressReport->newreport->projectType->title; // Standardize to uppercase
                    });
                Log::info('Existing history records', ['existing_history_keys' => $existingHistory->keys()->all()]);

                $newRecords = [];
                $updateRecords = [];
                $historyRecords = [];

                foreach ($data as $item) {
                    $projectname = $item['proyek_type'];
                    $unitlokal = $item['unit'];
                    $nodokumen = strtoupper($item['nodokumen']); // Standardize to uppercase
                    $countnodoc = Newprogressreport::where('nodokumen', $nodokumen)
                        ->whereHas('newreport.projectType', function ($q) use ($projectname) {
                            $q->where('title', '!=', $projectname);
                        })->count();
                    $releasedagain = $countnodoc == 0 ? 0 : 1;
                    Log::info('Processing item', [
                        'nodokumen' => $nodokumen,
                        'proyek_type' => $projectname,
                        'rev' => $item['rev'],
                        'releasedagain' => $releasedagain
                    ]);

                    $jenisdokumen = $item['jenisdokumen'];
                    $documentkind = $allDocumentKinds[$jenisdokumen] ?? null;
                    $rev = $item['rev'] ?? "";
                    $key = $nodokumen . '-' . $rev . '-' . $projectname . '-' . $unitlokal;

                    $realisasiDate = null;
                    if (!empty($item['realisasi'])) {
                        try {
                            $realisasiDate = new DateTime($item['realisasi']);
                        } catch (Exception $e) {
                            Log::warning('Invalid realisasi date', ['nodokumen' => $nodokumen, 'realisasi' => $item['realisasi'], 'error' => $e->getMessage()]);
                            $realisasiDate = null;
                        }
                    }

                    $compositeKey = $nodokumen . '-' . $newreport_id;
                    if (isset($existingReports[$compositeKey])) {
                        $existingRecord = $existingReports[$compositeKey];
                        Log::info('Found existing record', ['composite_key' => $compositeKey]);

                        $existingRealisasiDate = null;
                        if (!empty($existingRecord->realisasi)) {
                            try {
                                $existingRealisasiDate = new DateTime($existingRecord->realisasi);
                            } catch (Exception $e) {
                                Log::warning('Invalid existing realisasi date', ['nodokumen' => $nodokumen, 'realisasi' => $existingRecord->realisasi, 'error' => $e->getMessage()]);
                            }
                        }

                        if (
                            $existingRecord->releasedagain != $releasedagain ||
                            ($realisasiDate && (!$existingRealisasiDate || $realisasiDate > $existingRealisasiDate))
                        ) {
                            $existingRecord->namadokumen = $item['namadokumen'];
                            $existingRecord->papersize = $item['papersize'];
                            $existingRecord->sheet = $item['sheet'];
                            $existingRecord->drafter = $item['drafter'];
                            $existingRecord->checker = $item['checker'];
                            $existingRecord->realisasi = $item['realisasi'];
                            $existingRecord->realisasidate = $item['realisasidate'];
                            $existingRecord->dcr = $item['dcr'] ?? "";
                            if ($documentkind) {
                                $existingRecord->documentkind_id = $documentkind->id;
                            }
                            $existingRecord->status = $item['status'] ?? $existingRecord->status;
                            $existingRecord->releasedagain = $releasedagain;
                            $updateRecords[] = $existingRecord;
                            $exportedCount++;
                            Log::info('Record marked for update', ['nodokumen' => $nodokumen, 'newreport_id' => $newreport_id]);
                        }

                        if (!isset($existingHistory[$key]) && isset($item['rev'])) {
                            $data = [
                                'newprogressreport_id' => $existingRecord->id,
                                'nodokumen' => $nodokumen,
                                'namadokumen' => $item['namadokumen'],
                                'papersize' => $item['papersize'],
                                'sheet' => $item['sheet'],
                                'drafter' => $item['drafter'],
                                'checker' => $item['checker'],
                                'realisasi' => $item['realisasi'],
                                'realisasidate' => $item['realisasidate'],
                                'documentkind_id' => $documentkind ? $documentkind->id : null,
                                'rev' => $item['rev'],
                                'dcr' => $item['dcr'] ?? "",
                                'status' => $item['status'] ?? "",
                                'created_at' => now(),
                                'updated_at' => now(),
                                'fileid' => $item['fileid'] ?? null,
                            ];
                            $historyRecords[] = $data;
                            Log::info('History record prepared', ['key' => $key]);
                        }
                    } else {
                        $temporarydata = [
                            'newreport_id' => $newreport_id,
                            'nodokumen' => $nodokumen,
                            'namadokumen' => $item['namadokumen'],
                            'papersize' => $item['papersize'],
                            'sheet' => $item['sheet'],
                            'drafter' => $item['drafter'],
                            'checker' => $item['checker'],
                            'documentkind_id' => $documentkind ? $documentkind->id : null,
                            'realisasi' => $item['realisasi'] ?? '',
                            'realisasidate' => $item['realisasidate'],
                            'dcr' => $item['dcr'] ?? "",
                            'status' => $item['status'] ?? '',
                            'created_at' => now(),
                            'updated_at' => now(),
                            'releasedagain' => $releasedagain,
                        ];
                        $newRecords[] = $temporarydata;
                        $exportedCount++;
                        Log::info('New record prepared', ['nodokumen' => $nodokumen, 'newreport_id' => $newreport_id]);

                        $data = [
                            'newprogressreport_id' => null,
                            'nodokumen' => $nodokumen,
                            'namadokumen' => $item['namadokumen'],
                            'papersize' => $item['papersize'],
                            'sheet' => $item['sheet'],
                            'drafter' => $item['drafter'],
                            'checker' => $item['checker'],
                            'realisasi' => $item['realisasi'],
                            'realisasidate' => $item['realisasidate'],
                            'documentkind_id' => $documentkind ? $documentkind->id : null,
                            'rev' => $item['rev'] ?? "",
                            'dcr' => $item['dcr'] ?? "",
                            'status' => $item['status'] ?? "",
                            'created_at' => now(),
                            'updated_at' => now(),
                            'fileid' => $item['fileid'] ?? null,
                        ];
                        $historyRecords[] = $data;
                        Log::info('History record prepared for new record', ['nodokumen' => $nodokumen, 'rev' => $item['rev']]);
                    }
                }

                if (!empty($updateRecords)) {
                    foreach ($updateRecords as $record) {
                        $record->save();
                        Log::info('Record updated', ['nodokumen' => $record->nodokumen, 'newreport_id' => $record->newreport_id]);
                    }
                }

                $newRecordsNoDouble = []; // Initialize to prevent undefined variable
                if (!empty($newRecords)) {
                    $uniqueRecords = [];
                    foreach ($newRecords as $record) {
                        $nodokumen = $record['nodokumen'];
                        $newreport_id = $record['newreport_id'];
                        $uniqueKey = $nodokumen . '-' . $newreport_id;

                        if (!isset($uniqueRecords[$uniqueKey])) {
                            $uniqueRecords[$uniqueKey] = $record;
                        } else {
                            Log::warning('Duplicate record found in newRecords', ['unique_key' => $uniqueKey]);
                            $exportedCount -= 1;
                        }
                    }

                    $newRecordsFiltered = array_filter($uniqueRecords, function ($record) use ($existingNodokumenInDB) {
                        $uniqueKey = $record['nodokumen'] . '-' . $record['newreport_id'];
                        if (in_array($uniqueKey, $existingNodokumenInDB)) {
                            Log::warning('Record already exists in database, skipping insertion', ['unique_key' => $uniqueKey]);

                            return false;
                        }
                        return true;
                    });

                    $newRecordsNoDouble = array_values($newRecordsFiltered);
                    Log::info('Records to be inserted into newprogressreports', [
                        'records' => array_map(function ($record) {
                            return ['nodokumen' => $record['nodokumen'], 'newreport_id' => $record['newreport_id']];
                        }, $newRecordsNoDouble)
                    ]);

                    if (!empty($newRecordsNoDouble)) {
                        try {
                            Newprogressreport::insert($newRecordsNoDouble);
                            Log::info('Records inserted into newprogressreports', ['count' => count($newRecordsNoDouble)]);
                        } catch (Exception $e) {
                            Log::error('Error inserting newprogressreports', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
                            throw $e;
                        }
                    }

                    $lastInsertedIds = Newprogressreport::where('newreport_id', $newreport_id)
                        ->whereIn('nodokumen', array_column($newRecordsNoDouble, 'nodokumen'))
                        ->pluck('id', 'nodokumen');
                    Log::info('Last inserted IDs', ['last_inserted_ids' => $lastInsertedIds->toArray()]);

                    foreach ($historyRecords as &$history) {
                        if (!isset($history['newprogressreport_id']) || !$history['newprogressreport_id']) {
                            $history['newprogressreport_id'] = $lastInsertedIds[$history['nodokumen']] ?? null;
                            if (!$history['newprogressreport_id']) {
                                Log::warning('No newprogressreport_id for history record', ['nodokumen' => $history['nodokumen']]);
                                continue;
                            }
                        }
                    }
                } else {
                    Log::info('No new records to insert into newprogressreports', ['newreport_id' => $newreport_id]);
                }

                if (!empty($historyRecords)) {
                    try {
                        Newprogressreporthistory::insert($historyRecords);
                        Log::info('History records inserted', ['count' => count($historyRecords)]);
                    } catch (Exception $e) {
                        Log::error('Error inserting history records', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
                        throw $e;
                    }
                }

                $projectandvalue = Newreport::calculatelastpercentage();
                Log::info('Percentage calculated', ['percentage' => $projectandvalue]);

                $newreport->systemLogs()->create([
                    'message' => json_encode([
                        'message' => 'Data Excel successfully imported',
                        'updatedata' => array_map(function ($record) {
                            return ['nodokumen' => $record->nodokumen, 'newreport_id' => $record->newreport_id];
                        }, $updateRecords),
                        'databaru' => array_map(function ($record) {
                            return ['nodokumen' => $record['nodokumen'], 'newreport_id' => $record['newreport_id']];
                        }, $newRecordsNoDouble),
                        'persentase' => $projectandvalue[0],
                        'persentase_internal' => $projectandvalue[1],
                    ]),
                    'level' => 'info',
                    'user' => auth()->user()->name,
                    'user_id' => auth()->user()->id,
                    'aksi' => 'progressaddition',
                ]);
                Log::info('System log created', ['newreport_id' => $newreport->id]);
            }

            if (env('APP_NAME') == 'inka_local') {
                $this->unclearpdfdownload();
                Log::info('unclearpdfdownload executed');
            }

            DB::commit();
            Log::info('Transaction committed', ['stringkiriman' => $stringkiriman]);

            return response()->json(['message' => 'Data Excel successfully imported: ' . $stringkiriman, 'exported_count' => $exportedCount], 200);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Transaction failed', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json(['error' => 'Failed to process Excel file: ' . $e->getMessage()], 500);
        }
    }

    public function extractFileId($url)
    {
        $query = parse_url($url, PHP_URL_QUERY); // ambil bagian query dari URL
        parse_str($query, $params); // konversi jadi array key => value

        return $params['fileId'] ?? null; // ambil fileId
    }

    public function progressreportexported($importedData)
    {
        $revisiData = [];
        $seenKeys = []; // Track unique nodokumen-proyek_type combinations
        foreach ($importedData as $key => $row) {
            $proyek_type = trim($row[1] ?? ""); //B
            $nodokumen = trim($row[2] ?? "");
            $rev = trim($row[7] ?? "");
            $uniqueKey = $nodokumen . '-' . $proyek_type . '-' . $rev;
            // Skip if this nodokumen-proyek_type combination is already processed
            if (in_array($uniqueKey, $seenKeys)) {
                continue;
            }
            $seenKeys[] = $uniqueKey;

            $namadokumen = trim($row[3] ?? "");
            $papersize = trim($row[5] ?? "");
            $sheet = trim($row[6] ?? "");


            $realisasi = $this->transformDate(trim($row[10] ?? ""));
            $drafter = trim($row[11] ?? "");
            $checker = trim($row[12] ?? "");
            $jenisdokumen = trim($row[15] ?? "");

            $dcr = trim($row[16] ?? "");
            $status = trim($row[17] ?? "");
            $fileurl = trim($row[18] ?? '');
            if ($fileurl === '') {
                // Handle empty file URL case
                $fileid = null;
            } else {
                $fileid = $this->extractFileId($fileurl);
            }

            $unit = $this->perpanjangan(trim($row[4] ?? ""));



            // Validate and parse realisasidate
            $realisasidate = null;
            $realisasiRaw = $row[10] ?? null;        // JANGAN trim dulu — biarkan detect type

            if (!empty($realisasiRaw)) {
                if ($realisasiRaw instanceof \DateTimeInterface) {
                    $realisasidate = Carbon::instance($realisasiRaw)->startOfDay();
                } elseif (is_numeric($realisasiRaw)) {
                    $realisasidate = Carbon::create(1899, 12, 30)
                        ->addDays($realisasiRaw)
                        ->startOfDay();
                } else {
                    $realisasiRaw = trim($realisasiRaw);   // string
                    $formats = [
                        'd-m-Y',         // 13-06-2025
                        'd-M-y',         // 13-Jun-25  ← TAMBAHKAN INI
                        'd-M-Y',         // 13-Jun-2025
                        'Y-m-d',
                        'd/m/Y',
                        'm/d/Y',
                    ];

                    foreach ($formats as $fmt) {
                        if (Carbon::hasFormat($realisasiRaw, $fmt)) {
                            $realisasidate = Carbon::createFromFormat($fmt, $realisasiRaw)
                                ->startOfDay();
                            break;
                        }
                    }
                }
            }



            // Skip this row if rev is empty
            if ($rev !== '') {
                // Validate status
                $hasil = $this->validateRev($rev);
                if ($hasil == true && $proyek_type != "" && $nodokumen != "" && $namadokumen != "" && $jenisdokumen != "") {
                    if ($status != "RELEASED") {
                        throw new Exception("Status must be 'RELEASED' for document with No. Dokumen: {$nodokumen}");
                    }
                    $allowedUnits = [
                        'MTPR',
                        'Quality Engineering',
                        'Electrical Engineering System',
                        'Mechanical Engineering System',
                        'Product Engineering',
                        'Desain Elektrik',
                        'Desain Interior',
                        'Desain Carbody',
                        'Sistem Mekanik',
                        'Desain Bogie & Wagon',
                        'Preparation & Support',
                        'Shop Drawing',
                        'Teknologi Proses',
                        'Welding Technology'
                    ];

                    if (!in_array($unit, $allowedUnits)) {
                        throw new Exception(
                            "Unit must be one of: " . implode(', ', $allowedUnits) .
                            " for document with No. Dokumen: {$nodokumen}"
                        );
                    }

                    if ($jenisdokumen == "") {
                        throw new Exception("Jenis Dokumen cannot be empty for document with No. Dokumen: {$nodokumen}");
                    }
                    if ($realisasi == "") {
                        throw new Exception("Realisasi cannot be empty for document with No. Dokumen: {$nodokumen}");
                    }

                    if ($jenisdokumen == "") {
                        throw new Exception("Jenis Dokumen cannot be empty for document with No. Dokumen: {$nodokumen}");
                    }
                    if ($realisasi == "") {
                        throw new Exception("Realisasi cannot be empty for document with No. Dokumen: {$nodokumen}");
                    }
                    if ($realisasidate == null) {
                        throw new Exception("Realisasi Date cannot be empty for document with No. Dokumen: {$nodokumen}");
                    }

                    if (!in_array($papersize, ['A4', 'A3', 'A2', 'A1', 'A0'])) {
                        throw new Exception("Papersize must be one of 'A4', 'A3', 'A2', 'A1', 'A0' for document with No. Dokumen: {$nodokumen}");
                    }
                    if (!is_numeric($sheet) || $sheet < 1) {
                        throw new Exception("Sheet must be a positive number for document with No. Dokumen: {$nodokumen}");
                    }
                    if (!in_array(($rev), ['0', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'AA', 'AB', 'AC', 'AD', 'AE', 'AF', 'AG', 'AH', 'AI', 'AJ', 'AK', 'AL', 'AM', 'AN', 'AO', 'AP', 'AQ', 'AR', 'AS', 'AT', 'AU', 'AV', 'AW', 'AX', 'AY', 'AZ'])) {
                        throw new Exception("Rev must be one of '0', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'AA', 'AB', 'AC', 'AD', 'AE', 'AF', 'AG', 'AH', 'AI', 'AJ', 'AK', 'AL', 'AM', 'AN', 'AO', 'AP', 'AQ', 'AR', 'AS', 'AT', 'AU', 'AV', 'AW', 'AX', 'AY', 'AZ' for document with No. Dokumen: {$nodokumen}");
                    }
                    $revisiData[] = [
                        'proyek_type' => $proyek_type,
                        'nodokumen' => $nodokumen,
                        'namadokumen' => $namadokumen,
                        'jenisdokumen' => $jenisdokumen,
                        'realisasi' => $realisasi,
                        'realisasidate' => $realisasidate,
                        'papersize' => $papersize,
                        'sheet' => $sheet,
                        'rev' => (string) $rev,
                        'drafter' => $drafter,
                        'checker' => $checker,
                        'unit' => $unit,
                        'dcr' => $dcr,
                        'status' => $status,
                        'fileid' => $fileid,
                    ];
                } else {
                }
            }
        }
        return $revisiData;
    }

    private function validateRev($rev)
    {
        // Ubah $rev ke string dulu agar preg_match bisa jalan
        $revStr = (string) $rev;

        // Cek apakah rev tidak kosong dan hanya mengandung huruf A-Z atau angka 0
        if (preg_match('/^[A-Z0]*$/', $revStr)) {
            return true;
        } else {
            return false;
        }
    }

    // format progress report khusus
    // untuk progress report yang sudah ada di database
    // digunakan untuk mengupdate progress report yang sudah ada
    public function formatprogresskhusus(Request $request)
    {
        $validatedData = $request->validate([
            'file' => 'required|file|mimes:xlsx,xls',
        ]);

        $exportedCount = 0;
        $file = $request->file('file');
        $listproject = Cache::remember('list_project_titles', 180, function () {
            return ProjectType::pluck('title')->all();
        });


        try {
            $import = new RawprogressreportsImport();
            $revisiData = Excel::toCollection($import, $file)->first();

            if (empty($revisiData)) {
                return response()->json(['error' => 'No data found in the Excel file.'], 400);
            }

            $processedData = $this->progressreportexportedkhusus($revisiData);

            $groupedData = [];



            foreach ($processedData as $item) {
                $proyek_type = trim($item['proyek_type']);
                $unit = $item['unit'];
                $groupKey = $proyek_type . '-' . $unit;

                if (in_array($proyek_type, $listproject)) {
                    if (!isset($groupedData[$groupKey])) {
                        $groupedData[$groupKey] = [];
                    }
                    $groupedData[$groupKey][] = $item;
                }
            }

            $nodokumenList = array_column($processedData, 'nodokumen');
            $allProjectTypes = ProjectType::whereIn('title', array_unique(array_column($processedData, 'proyek_type')))
                ->get()
                ->keyBy('title');

            $allDocumentKinds = NewProgressReportDocumentKind::pluck('id', 'name');
            $allDocumentLevels = NewProgressReportsLevel::pluck('id', 'title');
            $allUnit = NewprogressreportUnit::whereIn('name', array_unique(array_column($processedData, 'unit')))
                ->get()
                ->keyBy('name');


            $stringkiriman = "";

            foreach ($groupedData as $groupKey => $data) {
                $stringkiriman .= $groupKey . " ";
                list($proyek_type, $unit) = explode('-', $groupKey);
                $project = $allProjectTypes[$proyek_type] ?? null;
                if (!$project) {
                    continue;
                }
                $unitModel = $allUnit[$unit] ?? null;
                if (!$unitModel) {
                    continue;
                }
                $progressreport = Newreport::firstOrCreate(
                    [
                        'proyek_type_id' => $project->id,
                        'unit_id' => $unitModel->id
                    ],
                    [
                        'unit' => $unit,
                        'status' => 'Terbuka'
                    ]
                );

                $id = $progressreport->id;




                // Ambil existing reports hanya untuk newreport_id saat ini
                $existingReports = Newprogressreport::with(['histories', 'newreport'])
                    ->where('newreport_id', $id)
                    ->whereIn('nodokumen', array_column($data, 'nodokumen')) // Hanya nodokumen di grup ini
                    ->get()
                    ->keyBy('nodokumen');

                $newRecords = [];
                $updateRecords = [];
                $updateHistoryRecords = [];

                $bulkInsertData = [];

                foreach ($data as $item) {
                    // Pastikan nilai tidak mengandung '#N/A' atau format tidak valid
                    $papersize = isset($item['papersize']) && $item['papersize'] !== '#N/A' ? $item['papersize'] : null;
                    $sheet = isset($item['sheet']) && is_numeric($item['sheet']) ? $item['sheet'] : null;

                    $nodokumen = $item['nodokumen'];
                    $documentkind_id = $allDocumentKinds[trim($item['jenisdokumen'])] ?? null;
                    $documentlevel_id = $allDocumentLevels[trim($item['level'])] ?? null;
                    $startreleasedate = $item['startreleasedate'] ?? null;
                    $deadlinereleasedate = $item['deadlinereleasedate'] ?? null;
                    $realisasidate = $item['realisasidate'] ?? null;
                    $status = $item['status'] ?? null;

                    if (isset($existingReports[$nodokumen])) {
                        $existingRecord = $existingReports[$nodokumen];
                        $oldData = $existingRecord->toArray();

                        if ($startreleasedate) {
                            $existingRecord->startreleasedate = $startreleasedate;
                        }
                        if ($deadlinereleasedate) {
                            $existingRecord->deadlinereleasedate = $deadlinereleasedate;
                        }
                        if ($realisasidate) {
                            $existingRecord->realisasidate = $realisasidate;
                        }
                        if ($documentkind_id) {
                            $existingRecord->documentkind_id = $documentkind_id;
                        }
                        if ($documentlevel_id) {
                            $existingRecord->level_id = $documentlevel_id;
                        }
                        if ($papersize) {
                            $existingRecord->papersize = $papersize;
                        }
                        if ($sheet) {
                            $existingRecord->sheet = $sheet;
                        }
                        if ($status) {
                            $existingRecord->status = $status;
                        }

                        $newData = $existingRecord->toArray();

                        if ($oldData != $newData) {
                            $existingRecord->save();
                        }

                        if ($existingRecord->histories->isNotEmpty()) {
                            $lastHistory = $existingRecord->histories->last();

                            if ($lastHistory->rev == $item['rev']) {
                                $oldData = $lastHistory->toArray();
                                $updateData = [];

                                // Hanya update jika nilai berubah
                                if ($startreleasedate !== null && $oldData['startreleasedate'] !== $startreleasedate) {
                                    $updateData['startreleasedate'] = $startreleasedate;
                                }
                                if ($deadlinereleasedate !== null && $oldData['deadlinereleasedate'] !== $deadlinereleasedate) {
                                    $updateData['deadlinereleasedate'] = $deadlinereleasedate;
                                }
                                if ($realisasidate !== null && $oldData['realisasidate'] !== $realisasidate) {
                                    $updateData['realisasidate'] = $realisasidate;
                                }
                                if ($documentkind_id !== null && $oldData['documentkind_id'] !== $documentkind_id) {
                                    $updateData['documentkind_id'] = $documentkind_id;
                                }
                                if ($documentlevel_id !== null && $oldData['level_id'] !== $documentlevel_id) {
                                    $updateData['level_id'] = $documentlevel_id;
                                }
                                if ($papersize !== null && $oldData['papersize'] !== $papersize) {
                                    $updateData['papersize'] = $papersize;
                                }
                                if ($sheet !== null && $oldData['sheet'] !== $sheet) {
                                    $updateData['sheet'] = $sheet;
                                }

                                // Hanya update jika ada perubahan data
                                if (!empty($updateData)) {
                                    $updateResult = $lastHistory->update($updateData);
                                    if ($updateResult) {
                                        $updateHistoryRecords[] = 'dokumenupdate:' . $lastHistory->id . $lastHistory->nodokumen;
                                    }
                                }
                            }


                            $unitsnontp = [
                                "Desain Bogie & Wagon",
                                "Desain Carbody",
                                "Desain Elektrik",
                                "Desain Interior",
                                "Desain Mekanik",
                                "Product Engineering",
                                "Mechanical Engineering System",
                                "Quality Engineering",
                                "Electrical Engineering System"
                            ];

                            foreach ($existingRecord->histories as $history) {
                                $oldData = $history->toArray();
                                $updateData = [];

                                // Hanya update jika nilai berubah
                                if ($startreleasedate !== null && $oldData['startreleasedate'] !== $startreleasedate) {
                                    $updateData['startreleasedate'] = $startreleasedate;
                                }
                                if ($deadlinereleasedate !== null && $oldData['deadlinereleasedate'] !== $deadlinereleasedate) {
                                    $updateData['deadlinereleasedate'] = $deadlinereleasedate;
                                }
                                if ($realisasidate !== null && $oldData['realisasidate'] !== $realisasidate) {
                                    $updateData['realisasidate'] = $realisasidate;
                                }
                                if ($documentkind_id !== null && $oldData['documentkind_id'] !== $documentkind_id) {
                                    $updateData['documentkind_id'] = $documentkind_id;
                                }
                                if ($documentlevel_id !== null && $oldData['level_id'] !== $documentlevel_id) {
                                    $updateData['level_id'] = $documentlevel_id;
                                }
                                if ($papersize !== null && $oldData['papersize'] !== $papersize) {
                                    $updateData['papersize'] = $papersize;
                                }
                                if ($sheet !== null && $oldData['sheet'] !== $sheet) {
                                    $updateData['sheet'] = $sheet;
                                }

                                // Hanya update jika ada perubahan data
                                if (!empty($updateData)) {
                                    $updateResult = $history->update($updateData);
                                    if ($updateResult) {
                                        $updateHistoryRecords[] = 'dokumenupdate:' . $history->id . $history->nodokumen;
                                    }
                                }
                            }
                        } else {
                            $allowedRev = array_merge(['0'], range('A', 'Z'));
                            $rev = strtoupper($item['rev'] ?? '0');

                            if (isset($existingRecord) && in_array($rev, $allowedRev)) {
                                $bulkInsertData[] = [
                                    'newprogressreport_id' => $existingRecord->id,
                                    'nodokumen' => $nodokumen,
                                    'namadokumen' => $item['namadokumen'] ?? null,
                                    'rev' => $rev,
                                    'created_at' => now(),
                                    'updated_at' => now(),
                                ];
                            }
                        }

                        $updateRecords[] = $existingRecord;
                        $exportedRecords[] = $item;
                    }
                }

                if (!empty($bulkInsertData)) {
                    try {
                        Newprogressreporthistory::insert($bulkInsertData);
                        foreach ($bulkInsertData as $insertedItem) {
                            $updateHistoryRecords[] = 'suksesdokumenbaru:' . $insertedItem['nodokumen'];
                        }
                    } catch (Exception $e) {
                        $updateHistoryRecords[] = 'gagaldokumenbaru:' . $e->getMessage();
                    }
                }



                // Update records in the database
                foreach ($updateRecords as $record) {
                    $record->save();
                }

                // Update project percentage and log
                $projectandvalue = Newreport::calculatelastpercentage();
                $progressreport->systemLogs()->create([
                    'message' => json_encode([
                        'message' => 'Data Excel successfully imported',
                        'updatedata' => $updateRecords,
                        'databaru' => $newRecords,
                        'persentase' => $projectandvalue[0],
                        'persentase_internal' => $projectandvalue[1],
                    ]),
                    'level' => 'info',
                    'user' => auth()->user()->name,
                    'user_id' => auth()->user()->id,
                    'aksi' => 'progressaddition',
                ]);
            }

            return response()->json(['message' => 'Data Excel successfully imported: ' . $stringkiriman . json_encode($updateHistoryRecords)], 200);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function progressreportexportedkhusus($importedData)
    {
        $revisiData = [];
        $listproject = ProjectType::all()->keyBy('title');
        foreach ($importedData as $key => $row) {

            $proyek_type = trim($row[2] ?? "");
            $unit = trim($row[3] ?? "");
            $nodokumen = trim($row[4] ?? "");
            $namadokumen = trim($row[5] ?? "");
            $rev = trim($row[6] ?? "");
            $level = trim($row[7] ?? "");
            $drafter = trim($row[8] ?? "");
            $checker = trim($row[9] ?? "");

            // Validate and parse startreleasedate
            $startReleaseRaw = trim($row[10] ?? "");
            $startreleasedate = null; // Default null

            if (!empty($startReleaseRaw)) {
                if (is_numeric($startReleaseRaw)) {
                    // Konversi dari serial Excel ke tanggal
                    $baseDate = Carbon::createFromFormat('Y-m-d', '1900-01-01');
                    $startreleasedate = $baseDate->addDays($startReleaseRaw - 2)->setTime(0, 0, 0);
                } elseif (Carbon::hasFormat($startReleaseRaw, 'd-m-Y')) {
                    try {
                        $startreleasedate = Carbon::createFromFormat('d-m-Y', $startReleaseRaw)->setTime(0, 0, 0);
                    } catch (Exception $e) {
                        $startreleasedate = null; // Tetap null jika format salah
                    }
                }
            }


            // Validate and parse deadlinereleasedate
            $deadlinereleasedate = null;
            $deadlineRaw = trim($row[11] ?? "");

            if (!empty($deadlineRaw)) {
                if (is_numeric($deadlineRaw)) {
                    // Konversi dari serial Excel ke tanggal
                    $baseDate = Carbon::createFromFormat('Y-m-d', '1900-01-01');
                    $deadlinereleasedate = $baseDate->addDays($deadlineRaw - 2)->setTime(0, 0, 0);  // Waktu 00:00:00
                } elseif (Carbon::hasFormat($deadlineRaw, 'd-m-Y')) {
                    $deadlinereleasedate = Carbon::createFromFormat('d-m-Y', $deadlineRaw)->setTime(0, 0, 0); // Waktu 00:00:00
                }
            }



            // Validate and parse realisasidate
            $realisasidate = null;
            $realisasiRaw = trim($row[12] ?? "");

            if (!empty($realisasiRaw)) {
                if (is_numeric($realisasiRaw)) {
                    // Konversi dari serial Excel ke tanggal
                    $baseDate = Carbon::createFromFormat('Y-m-d', '1900-01-01');
                    $realisasidate = $baseDate->addDays($realisasiRaw - 2)->setTime(0, 0, 0);  // Waktu 00:00:00
                } elseif (Carbon::hasFormat($realisasiRaw, 'd-m-Y')) {
                    $realisasidate = Carbon::createFromFormat('d-m-Y', $realisasiRaw)->setTime(0, 0, 0); // Waktu 00:00:00
                }
            }

            // Parse realisasi date with the transformDate function
            $realisasi = $this->transformDate(trim($row[12] ?? ""));

            $jenisdokumen = trim($row[13] ?? "");
            $status = trim($row[14] ?? "");
            $papersize = trim($row[15] ?? "");
            $sheet = trim($row[16] ?? "");

            // if (!empty($proyek_type) && empty($jenisdokumen)) {
            //     $rejectAll = true;
            //     break; // Keluar dari loop, tidak perlu lanjutkan
            // }

            // Skip this row if rev is empty
            if ($rev !== '' && isset($listproject[$proyek_type])) {
                $revisiData[] = [
                    'proyek_type' => $proyek_type, // Add proyek_type to the array
                    'proyek_type_id' => $listproject[$proyek_type]->id,
                    'nodokumen' => $nodokumen,
                    'namadokumen' => $namadokumen,
                    'rev' => $rev,
                    'drafter' => $drafter,
                    'checker' => $checker,
                    'deadlinereleasedate' => $deadlinereleasedate ? $deadlinereleasedate->format('Y-m-d H:i:s') : null,
                    'realisasidate' => $realisasidate ? $realisasidate->format('Y-m-d H:i:s') : null,
                    'realisasi' => $realisasi,
                    'jenisdokumen' => $jenisdokumen,
                    'status' => $status,
                    'startreleasedate' => $startreleasedate ? $startreleasedate->format('Y-m-d H:i:s') : null,
                    'unit' => $unit,
                    'level' => $level,
                    'papersize' => $papersize,
                    'sheet' => $sheet,
                ];
            }
        }
        return $revisiData;
    }

    // format update link 
    // digunakan untuk mengupdate link fileid pada history progress report
    public function formatupdatelink(Request $request)
    {
        $validatedData = $request->validate([
            'file' => 'required|file|mimes:xlsx,xls',
        ]);

        $file = $request->file('file');
        $import = new RawprogressreportsImport();
        $revisiData = Excel::toCollection($import, $file)->first();

        if (empty($revisiData)) {
            return response()->json(['error' => 'No data found in the Excel file.'], 400);
        }

        $processedData = $this->updatelinkexported($revisiData);

        if (empty($processedData)) {
            return response()->json(['error' => 'No valid data found in the Excel file.'], 400);
        }

        $report = [
            'success' => [],
            'skipped' => [],
            'not_found' => [],          // baru: history tidak ada
            'total_processed' => count($processedData),
            'updated' => 0,
        ];

        DB::beginTransaction();
        $projecttypeall = ProjectType::all()->keyBy('title');
        $unitall = Unit::all()->keyBy('name');

        try {
            $bulkUpdateData = [];

            foreach ($processedData as $item) {
                $nodokumen = $item['nodokumen'];
                $rev = $item['rev'];
                $fileid = $item['fileid'];
                $unitName = $item['unit'];
                $proyek_type = $item['proyek_type'];

                $key = "{$nodokumen} (Rev: {$rev})";

                // Cari proyek & unit
                $project = $projecttypeall->get($proyek_type);
                if (!$project) {
                    $report['skipped'][] = "$key → Proyek tidak ditemukan: $proyek_type";
                    continue;
                }

                $unit = $unitall->get($unitName);
                if (!$unit) {
                    $report['skipped'][] = "$key → Unit tidak ditemukan: $unitName";
                    continue;
                }

                // Cari Newreport
                $newreport = Newreport::where('proyek_type_id', $project->id)
                    ->where('unit_id', $unit->id)
                    ->first();

                if (!$newreport) {
                    $report['skipped'][] = "$key → Kombinasi Proyek/Unit tidak ditemukan";
                    continue;
                }

                // Cari Newprogressreport
                $newprogressreport = Newprogressreport::with('histories')->where('newreport_id', $newreport->id)
                    ->where('nodokumen', $nodokumen)
                    ->first();

                if (!$newprogressreport) {
                    $report['skipped'][] = "$key → No. Dokumen tidak ditemukan di database";
                    continue;
                }

                // CARI HISTORY YANG SUDAH ADA (hanya berdasarkan rev)
                $history = $newprogressreport->histories()
                    ->where('rev', $rev)
                    ->first();

                if (!$history) {
                    // HISTORY TIDAK ADA → kita skip (tidak buat baru)
                    $report['not_found'][] = "$key → History dengan Rev '$rev' belum ada (dilewati)";
                    continue;
                }

                // HISTORY ADA → update fileid
                $bulkUpdateData[] = [
                    'id' => $history->id,
                    'fileid' => $fileid,
                    'updated_at' => now(),
                ];

                $report['success'][] = "$key → Link diperbarui";
                $report['updated']++;
            }

            // LAKUKAN BULK UPDATE (sama seperti sebelumnya)
            if (!empty($bulkUpdateData)) {
                $ids = collect($bulkUpdateData)->pluck('id')->all();
                $cases = [];
                $updatedAtCases = [];

                foreach ($bulkUpdateData as $data) {
                    $cases[] = "WHEN id = {$data['id']} THEN " . ($data['fileid'] === null ? 'NULL' : "'{$data['fileid']}'");
                    $updatedAtCases[] = "WHEN id = {$data['id']} THEN '{$data['updated_at']}'";
                }

                $casesSql = implode(' ', $cases);
                $updatedAtSql = implode(' ', $updatedAtCases);

                DB::statement("
                UPDATE newprogressreporthistorys
                SET fileid = CASE {$casesSql} END,
                    updated_at = CASE {$updatedAtSql} END
                WHERE id IN (" . implode(',', $ids) . ")
            ");
            }

            // HAPUS SEMUA KODE INSERT (tidak ada lagi $bulkCreateData & insert)

            if (env('APP_NAME') == 'inka_local') {
                $this->unclearpdfdownload();
            }

            DB::commit();

            return response()->json([
                'message' => 'Proses update link selesai! (Hanya update, tidak ada insert)',
                'summary' => [
                    'total_baris_diproses' => $report['total_processed'],
                    'berhasil_diupdate' => $report['updated'],
                    'history_tidak_ditemukan' => count($report['not_found']),
                    'diskip' => count($report['skipped']),
                ],
                'detail_berhasil' => $report['success'],
                'history_tidak_ada' => $report['not_found'],
                'detail_diskip' => $report['skipped'],
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Format Update Link Error: ' . $e->getMessage());

            return response()->json([
                'error' => 'Terjadi kesalahan saat menyimpan data',
                'detail' => $e->getMessage()
            ], 500);
        }
    }

    public function updatelinkexported($importedData)
    {
        $revisiData = [];
        foreach ($importedData as $key => $row) {
            $proyek_type = trim($row[1] ?? ""); //B
            $unit = $this->perpanjangan(trim($row[4] ?? ""));
            $nodokumen = trim($row[2] ?? "");
            $fileurl = trim($row[18] ?? '');
            if ($fileurl === '') {
                // Handle empty file URL case
                $fileid = null;
            } else {
                $fileid = $this->extractFileId($fileurl);
            }
            $rev = trim($row[7] ?? "");

            // Skip this row if rev is empty
            if ($rev !== '') {
                // Validate status
                $hasil = $this->validateRev($rev);
                if ($hasil == true && $proyek_type != "" && $nodokumen != "") {

                    $allowedUnits = [
                        'MTPR',
                        'Quality Engineering',
                        'Electrical Engineering System',
                        'Mechanical Engineering System',
                        'Product Engineering',
                        'Desain Elektrik',
                        'Desain Interior',
                        'Desain Carbody',
                        'Sistem Mekanik',
                        'Desain Bogie & Wagon',
                        'Preparation & Support',
                        'Shop Drawing',
                        'Teknologi Proses',
                        'Welding Technology'
                    ];

                    if (!in_array($unit, $allowedUnits)) {
                        throw new Exception(
                            "Unit must be one of: " . implode(', ', $allowedUnits) .
                            " for document with No. Dokumen: {$nodokumen}"
                        );
                    }


                    if (!in_array(($rev), ['0', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'])) {
                        throw new Exception("Rev must be one of '0', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z' for document with No. Dokumen: {$nodokumen}");
                    }
                    $revisiData[] = [
                        'proyek_type' => $proyek_type,
                        'nodokumen' => $nodokumen,
                        'rev' => (string) $rev,
                        'unit' => $unit,
                        'fileid' => $fileid,
                    ];
                } else {
                }
            }
        }
        return $revisiData;
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
        } elseif ($namasingkatan == "MTPR") {
            return "MTPR";
        }
    }

    public function handleDeleteMultipleItems(Request $request)
    {
        $progressreportIds = $request->input('document_ids'); // Ambil ID dokumen

        if (empty($progressreportIds)) {
            return response()->json(['error' => 'Tidak ada dokumen yang dipilih untuk dihapus'], 400);
        }

        $progressReports = Newprogressreport::with('newprogressreporthistory')->whereIn('id', $progressreportIds)->get();

        if ($progressReports->isEmpty()) {
            return response()->json(['error' => 'Dokumen yang dipilih tidak ditemukan'], 404);
        }

        // Filter dokumen dengan status RELEASED
        $releasedReports = $progressReports->where('status', 'RELEASED');

        if ($releasedReports->isNotEmpty()) {
            return response()->json([
                'error' => 'Beberapa dokumen tidak dapat dihapus karena statusnya adalah RELEASED',
                'released_documents' => $releasedReports->pluck('id'),
            ], 400);
        }

        $newreportId = $progressReports->first()->newreport_id ?? null;

        if (!$newreportId) {
            return response()->json(['error' => 'ID Newreport tidak valid'], 404);
        }

        $newreport = Newreport::find($newreportId);

        if (!$newreport) {
            return response()->json(['error' => 'Newreport tidak ditemukan'], 404);
        }

        try {
            DB::beginTransaction();

            // Simpan log sebelum dihapus
            $progressReportsBeforeDelete = $progressReports->toArray();

            $projectAndValue = Newreport::calculatelastpercentage();

            $newreport->systemLogs()->create([
                'message' => json_encode([
                    'message' => 'Data dihapus',
                    'datasebelum' => $progressReportsBeforeDelete,
                    'datasesudah' => [],
                    'persentase' => $projectAndValue[0],
                    'persentase_internal' => $projectAndValue[1],
                ]),
                'level' => 'info',
                'user' => auth()->user()->name,
                'user_id' => auth()->id(),
                'aksi' => 'progressdelete',
            ]);

            // Hapus newprogressreporthistory terlebih dahulu
            foreach ($progressReports as $progressReport) {
                $progressReport->newprogressreporthistory()->delete();
            }

            // Hapus dokumen
            Newprogressreport::whereIn('id', $progressreportIds)->delete();

            DB::commit();

            return response()->json(['success' => 'Dokumen yang dipilih berhasil dihapus']);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'Gagal menghapus dokumen: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function handleUnreleaseMultipleItems(Request $request)
    {
        $progressreportIds = $request->input('document_ids'); // Ambil ID dokumen

        if (empty($progressreportIds)) {
            return response()->json(['error' => 'Tidak ada dokumen yang dipilih untuk dibatalkan rilisnya'], 400);
        }

        $progressReports = Newprogressreport::with('newprogressreporthistory')->whereIn('id', $progressreportIds)->get();

        if ($progressReports->isEmpty()) {
            return response()->json(['error' => 'Dokumen yang dipilih tidak ditemukan'], 404);
        }

        $newreportId = $progressReports->first()->newreport_id ?? null;

        if (!$newreportId) {
            return response()->json(['error' => 'ID Newreport tidak valid'], 404);
        }

        $newreport = Newreport::find($newreportId);

        if (!$newreport) {
            return response()->json(['error' => 'Newreport tidak ditemukan'], 404);
        }

        try {
            DB::beginTransaction();

            // Simpan log sebelum perubahan status
            $progressReportsBeforeUpdate = $progressReports->toArray();

            $projectAndValue = Newreport::calculatelastpercentage();

            $newreport->systemLogs()->create([
                'message' => json_encode([
                    'message' => 'Status dokumen dan riwayatnya diubah ke null (unreleased)',
                    'datasebelum' => $progressReportsBeforeUpdate,
                    'datasesudah' => $progressReports->map(function ($report) {
                        return array_merge($report->toArray(), ['status' => null]);
                    })->toArray(),
                    'persentase' => $projectAndValue[0],
                    'persentase_internal' => $projectAndValue[1],
                ]),
                'level' => 'info',
                'user' => auth()->user()->name,
                'user_id' => auth()->id(),
                'aksi' => 'progressunrelease',
            ]);

            // Update status ke null untuk newprogressreporthistory
            foreach ($progressReports as $progressReport) {
                $progressReport->newprogressreporthistory()->update(['status' => null]);
            }

            // Update status ke null untuk dokumen
            Newprogressreport::whereIn('id', $progressreportIds)->update(['status' => null]);

            DB::commit();

            return response()->json(['success' => 'Dokumen yang dipilih dan riwayatnya berhasil dibatalkan rilisnya']);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'Gagal membatalkan rilis dokumen: ' . $e->getMessage(),
            ], 500);
        }
    }
    public function handleReleaseMultipleItems(Request $request)
    {
        $progressreportIds = $request->input('document_ids'); // Ambil ID dokumen

        if (empty($progressreportIds)) {
            return response()->json(['error' => 'Tidak ada dokumen yang dipilih untuk dirilis'], 400);
        }

        $progressReports = Newprogressreport::with('newprogressreporthistory')->whereIn('id', $progressreportIds)->get();

        if ($progressReports->isEmpty()) {
            return response()->json(['error' => 'Dokumen yang dipilih tidak ditemukan'], 404);
        }

        $newreportId = $progressReports->first()->newreport_id ?? null;

        if (!$newreportId) {
            return response()->json(['error' => 'ID Newreport tidak valid'], 404);
        }

        $newreport = Newreport::find($newreportId);

        if (!$newreport) {
            return response()->json(['error' => 'Newreport tidak ditemukan'], 404);
        }

        try {
            DB::beginTransaction();

            // Simpan log sebelum perubahan status
            $progressReportsBeforeUpdate = $progressReports->toArray();

            $projectAndValue = Newreport::calculatelastpercentage();

            $newreport->systemLogs()->create([
                'message' => json_encode([
                    'message' => 'Status dokumen dan riwayatnya diubah ke RELEASED',
                    'datasebelum' => $progressReportsBeforeUpdate,
                    'datasesudah' => $progressReports->map(function ($report) {
                        return array_merge($report->toArray(), ['status' => 'RELEASED']);
                    })->toArray(),
                    'persentase' => $projectAndValue[0],
                    'persentase_internal' => $projectAndValue[1],
                ]),
                'level' => 'info',
                'user' => auth()->user()->name,
                'user_id' => auth()->id(),
                'aksi' => 'progressrelease',
            ]);

            // Update status ke RELEASED untuk newprogressreporthistory
            foreach ($progressReports as $progressReport) {
                $progressReport->newprogressreporthistory()->update(['status' => 'RELEASED']);
            }

            // Update status ke RELEASED untuk dokumen
            Newprogressreport::whereIn('id', $progressreportIds)->update(['status' => 'RELEASED']);

            DB::commit();

            return response()->json(['success' => 'Dokumen yang dipilih dan riwayatnya berhasil dirilis']);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'Gagal merilis dokumen: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function unlinkparent($id)
    {
        // Cari entitas berdasarkan id
        $newProgressReport = Newprogressreport::find($id);

        // Pastikan entitas ditemukan sebelum dihapus
        if ($newProgressReport) {
            // Hapus entitas
            $newProgressReport->parent_revision_id = null;
            $newProgressReport->save();
            // Redirect dengan pesan sukses jika berhasil
            return redirect()->route('newreports.index')->with('success', 'New progress report deleted successfully');
        } else {
            // Redirect dengan pesan error jika entitas tidak ditemukan
            return redirect()->route('newreports.index')->with('error', 'New progress report not found');
        }
    }

    // Fungsi otomatebom
    public function otomateprogressretrofit()
    {
        $allnodokumen = Newprogressreport::all(); // Mengambil data dari model Newprogressreport

        $scriptUrl = "https://script.google.com/macros/s/AKfycbyoNGII0-D7DYG-dzle4kd6hvRXkCdQ7aRH5laajnfQUWqHxhiTzdQyUIyWBxhlBErtYg/exec";

        // Ambil data dari API
        $response = file_get_contents($scriptUrl);
        $data = json_decode($response, true); // Mengubah JSON menjadi array asosiatif

        $updates = [];

        foreach ($allnodokumen as $nodokumen) {
            foreach ($data as $unit => $items) {
                foreach ($items as $item) {
                    if ($item['nodokumen'] == $nodokumen->nodokumen) {
                        if ($item['status'] == "EMPTY" && $nodokumen->status == "RELEASED") {
                            $updates[] = [
                                'Sheet' => $unit,
                                'nodokumen' => $nodokumen->nodokumen,
                                'newStatus' => $nodokumen->status,
                                'drafter' => $nodokumen->drafter,
                                'checker' => $nodokumen->checker,
                                'realisasi' => $nodokumen->realisasi,
                                'row' => $item['row'], // Baris yang relevan
                                'colStatus' => $item['colStatus'], // Kolom yang relevan
                                'colDrafter' => $item['colDrafter'], // Kolom yang relevan
                                'colChecker' => $item['colChecker'], // Kolom yang relevan
                                'colRealisasi' => $item['colRealisasi'], // Kolom yang relevan
                            ];
                        }
                    }
                }
            }
        }

        if (!empty($updates)) {
            $this->sendUpdatesToSpreadsheet($updates);
            return $updates;
        }
    }


    // Fungsi untuk mengirim pembaruan ke Google Apps Script
    private function sendUpdatesToSpreadsheet($updates)
    {
        // URL endpoint Apps Script untuk pembaruan
        $updateUrl = "https://script.google.com/macros/s/AKfycbwvss9f0fem-IYWJ4z76NYTbqHOMeWi4uWShb6MMGEoPp0zHm6KWgF0kMzsQBZ4e_78WQ/exec"; // Ganti dengan URL skrip Apps Anda

        // Menggunakan cURL untuk mengirim POST request
        $ch = curl_init($updateUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json'
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['updates' => $updates])); // Mengirim pembaruan dalam format JSON

        $response = curl_exec($ch);
        curl_close($ch);

        // Menangani response jika diperlukan
        // $responseData = json_decode($response, true);
        // Lakukan tindakan tambahan jika perlu
        return $updates;
    }

    public function storedokumentkind(Request $request)
    {
        // Validate the request data
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        // Create new JobticketDocumentKind
        $documentKind = NewProgressReportDocumentKind::create($validatedData);

        // Redirect or return a success response
        return redirect()->back()->with('success', 'Jobticket Document Kind created successfully!');
    }

    // Function to display all JobticketDocumentKinds (View)
    public function indexdokumentkind()
    {
        $documentKinds = NewProgressReportDocumentKind::with('unit')->get();
        $techUnits = NewprogressreportUnit::all();
        return view('newreports.documentkind', compact('documentKinds', 'techUnits'));
    }

    public function showSearchForm()
    {
        return view('newreports.search');
    }

    public function search(Request $request)
    {
        // Validasi input pencarian
        $request->validate([
            'query' => 'required|string|min:1',
        ]);

        // Ambil query dari input form
        $query = $request->input('query');

        // Lakukan pencarian berdasarkan nodokumen, namadokumen, level, atau drafter
        $results = NewProgressReport::where('nodokumen', 'LIKE', '%' . $query . '%')
            ->orWhere('namadokumen', 'LIKE', '%' . $query . '%')
            ->get();

        // Kembalikan hasil pencarian ke view
        return view('newreports.search_results', compact('results'));
    }


    public function indexnotifharian()
    {
        // Fetch all document kinds (id and name)
        $documentKinds = NewProgressReportDocumentKind::select('id', 'name')->get();

        // Fetch all NotifHarianUnit entries
        $notifHarianUnits = NotifHarianUnit::all()->map(function ($unit) {
            // Decode documentkind JSON and get the names
            $documentKindIds = json_decode($unit->documentkind, true);
            $documentKindNames = NewProgressReportDocumentKind::whereIn('id', $documentKindIds)->pluck('name')->toArray();

            // Add the names as a new property to the NotifHarianUnit
            $unit->documentkind_names = $documentKindNames;

            return $unit;
        });

        // Cache selama 3 jam (180 menit)
        $telegrammessagesaccounts = Cache::remember('telegrammessagesaccounts', 180, function () {
            return Wagroupnumber::all();
        });

        // Return the data to the view
        return view('newprogressreports.indexnotifharian', [
            'notifHarianUnits' => $notifHarianUnits,
            'documentKinds' => $documentKinds,
            'telegrammessagesaccounts' => $telegrammessagesaccounts
        ]);
    }

    // Function to show the edit form
    public function editnotifharian($id)
    {
        // Fetch the NotifHarianUnit by ID
        $notifHarianUnit = NotifHarianUnit::findOrFail($id);

        // Fetch all document kinds (id and name)
        $documentKinds = NewProgressReportDocumentKind::select('id', 'name')->get();

        // Decode documentkind JSON
        $selectedDocumentKinds = json_decode($notifHarianUnit->documentkind, true);

        // Cache selama 3 jam (180 menit)
        $telegrammessagesaccounts = Cache::remember('telegrammessagesaccounts', 180, function () {
            return Wagroupnumber::all();
        });

        return view('newprogressreports.editnotifharian', [
            'notifHarianUnit' => $notifHarianUnit,
            'documentKinds' => $documentKinds,
            'selectedDocumentKinds' => $selectedDocumentKinds,
            'telegrammessagesaccounts' => $telegrammessagesaccounts
        ]);
    }

    // Function to update the existing NotifHarianUnit
    public function updatenotifharian(Request $request, $id)
    {
        // Validasi input
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'documentkind' => 'required|array',
            'documentkind.*' => 'integer', // Pastikan setiap item dalam array adalah integer
            'telegrammessagesaccount_id' => 'nullable|integer' // Opsional dan harus integer
        ]);

        // Fetch the NotifHarianUnit by ID
        $notifHarianUnit = NotifHarianUnit::findOrFail($id);

        // Konversi array documentkind menjadi JSON
        $documentkindJson = json_encode(array_map('intval', $validatedData['documentkind']));

        // Update NotifHarianUnit
        $notifHarianUnit->update([
            'title' => $validatedData['title'],
            'documentkind' => $documentkindJson,
            'telegrammessagesaccount_id' => $validatedData['telegrammessagesaccount_id']
        ]);

        // Return redirect + pesan sukses
        return redirect()
            ->back()
            ->with('success', 'Notif harian berhasil diperbarui.');
    }

    // Function to delete the NotifHarianUnit
    public function deletenotifharian($id)
    {
        // Fetch the NotifHarianUnit by ID
        $notifHarianUnit = NotifHarianUnit::findOrFail($id);

        // Delete the NotifHarianUnit
        $notifHarianUnit->delete();

        return redirect()->route('newreports.index-notif-harian-units')
            ->with('success', 'Notif Harian Unit deleted successfully');
    }

    public function storenotifharian(Request $request)
    {
        // Validasi input
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'documentkind' => 'required|array',
            'documentkind.*' => 'integer', // Pastikan setiap item dalam array adalah integer
            'telegrammessagesaccount_id' => 'nullable|integer' // Opsional dan harus integer
        ]);

        // Konversi array documentkind menjadi JSON
        $documentkindJson = json_encode(array_map('intval', $validatedData['documentkind']));

        // Buat NotifHarianUnit baru
        $notifHarianUnit = NotifHarianUnit::create([
            'title' => $validatedData['title'],
            'documentkind' => $documentkindJson, // Simpan array dalam format JSON
            'telegrammessagesaccount_id' => $validatedData['telegrammessagesaccount_id'] // Simpan ID akun Telegram
        ]);

        return response()->json(['message' => 'Notif Harian Unit created successfully', 'data' => $notifHarianUnit], 201);
    }

    public function whatsappsend()
    {
        // Rentang waktu: 24 jam dari detik ini
        $startTime = now()->subDay(); // 24 jam yang lalu
        $endTime = now(); // Saat ini

        // Retrieve all NotifHarianUnit with related data using eager loading
        $notifHarianUnits = NotifHarianUnit::get();

        // Menyimpan unit yang berhasil diproses
        $successUnits = [];

        foreach ($notifHarianUnits as $notifHarianUnit) {
            $date = date('d-m-Y'); // Format tanggal d-m-Y
            $notifName = "Output_Teknologi_{$notifHarianUnit->id}_{$date}";

            // Cek apakah DailyNotification dengan name yang sama sudah ada
            $exists = DailyNotification::where('name', $notifName)->exists();

            if (!$exists) {
                $unit = $notifHarianUnit->title;

                // Buat DailyNotification baru
                $dailyNotification = DailyNotification::create([
                    'name' => $notifName,
                    'day' => now(),
                    'notif_harian_unit_id' => $notifHarianUnit->id,
                    'read_status' => 'unread'
                ]);

                $documentKinds = json_decode($notifHarianUnit->documentkind, true);

                if ($notifHarianUnit->title == "Fabrikasi Bogie" || $notifHarianUnit->title == "Fabrikasi Carbody") {
                    // Retrieve reports within the last 24 hours
                    $updatedReports = Newprogressreporthistory::whereBetween('realisasidate', [$startTime, $endTime])
                        ->whereIn('documentkind_id', $documentKinds)
                        ->get();
                } else {
                    // Retrieve reports within the last 24 hours
                    $updatedReports = Newprogressreporthistory::whereBetween('realisasidate', [$startTime, $endTime])
                        ->whereIn('documentkind_id', $documentKinds)
                        ->get();
                }


                // Mengambil array ID dari request atau menggunakan default
                $historyIds = $updatedReports->pluck('id')->toArray();

                // Menambahkan ID ke relasi newProgressReportHistories
                if (!empty($historyIds)) {
                    $dailyNotification->newProgressReportHistories()->attach($historyIds);
                }

                // Ambil data DailyNotification dengan eager loading yang lebih optimal
                $dailyNotification = DailyNotification::with([
                    'newProgressReportHistories.newProgressReport.newreport.projectType'
                ])->find($dailyNotification->id);

                if (!$dailyNotification) {
                    return response()->json(['message' => 'DailyNotification not found'], 404);
                }

                // Mengambil daftar nama dokumen berdasarkan ID
                $documentKindNames = NewProgressReportDocumentKind::pluck('name', 'id');

                // Mengelompokkan laporan berdasarkan jenis dokumen
                $documentview = $dailyNotification->newProgressReportHistories->groupBy(
                    fn($report) => $documentKindNames[$report->documentkind_id] ?? '📁 Dokumen Lainnya'
                );

                // Siapkan pesan untuk WhatsApp
                $message = "📢 *Laporan Ekspedisi Dokumen Terbaru!* 📢\n\n";
                $message .= "📂 *Daftar dokumen dapat diunduh melalui link berikut:*\n";
                $message .= "🔗 [Klik di sini](https://inka.goovicess.com/daily-notifications/show/{$dailyNotification->id})\n\n";

                if ($documentview->isEmpty()) {
                    $message .= "⚠️ *Dokumen kosong. Tidak ada laporan ekspedisi dokumen terbaru.*\n\n";
                } else {
                    foreach ($documentview as $documentKind => $reports) {
                        $message .= "📌 *{$documentKind}*\n";
                        $message .= "━━━━━━━━━━━━━━━━━━━━━━\n";

                        foreach ($reports as $report) {
                            $projectTitle = $report->newProgressReport->newreport->projectType->title;
                            $vaultLink = $report->newProgressReport->newreport->projectType->vault_link ?? 'Belum dicantumkan';


                            $message .= "🏗️ *Proyek:* {$projectTitle}\n";
                            $message .= "🌐 *Vault Link:* {$vaultLink}\n";
                            $message .= "📜 *No Dokumen:* {$report->nodokumen}\n";
                            $message .= "📄 *Nama Dokumen:* {$report->namadokumen}\n";
                            $message .= "🔄 *Revisi:* {$report->rev}\n";
                            $message .= "📑 *DCR:* {$report->dcr}\n";
                            $message .= "📌 *Status:* {$report->status}\n";
                            $message .= "━━━━━━━━━━━━━━━━━━━━━━\n";
                        }


                        $message .= "\n"; // Tambahkan spasi antar kategori
                    }
                }
                $message = substr($message, 0, 3800) . " ...";
                $message .= "🚀 *Silakan dikonfirmasi!* Terima kasih atas kerja sama dan dukungan Anda. 🙏😊";



                if (!$documentview->isEmpty()) {
                    // Kirim pesan WhatsApp karena Dokumen tersedia
                    TelegramService::ujisendunit($unit, $message);
                }


                // Menyimpan unit yang berhasil diproses
                $successUnits[] = $unit;
            }
        }

        // Mengembalikan daftar unit yang berhasil terbuat
        return "Unit yang berhasil terbuat: " . implode(', ', $successUnits);
    }

    public function whatsappsendproject48bogie()
    {
        $projectname = '48 Unit Bogie Train Merk F PT KAI';
        // Rentang waktu: 24 jam dari detik ini
        $startTime = now()->subDay(); // 24 jam yang lalu
        $endTime = now(); // Saat ini

        // Retrieve all NotifHarianUnit with related data using eager loading
        $notifHarianUnits = NotifHarianUnit::get();

        // Menyimpan unit yang berhasil diproses
        $successUnits = [];

        foreach ($notifHarianUnits as $notifHarianUnit) {
            $date = date('d-m-Y'); // Format tanggal d-m-Y
            $notifName = "Output_Teknologi_{$notifHarianUnit->id}_{$date}";

            // Cek apakah DailyNotification dengan name yang sama sudah ada
            $exists = DailyNotification::where('name', $notifName)->exists();

            if (!$exists) {
                $unit = $notifHarianUnit->title;

                // Buat DailyNotification baru
                $dailyNotification = DailyNotification::create([
                    'name' => $notifName,
                    'day' => now(),
                    'notif_harian_unit_id' => $notifHarianUnit->id,
                    'read_status' => 'unread'
                ]);

                $documentKinds = json_decode($notifHarianUnit->documentkind, true);

                // Retrieve reports within the last 24 hours
                $updatedReports = Newprogressreporthistory::whereBetween('created_at', [$startTime, $endTime])
                    ->whereIn('documentkind_id', $documentKinds)
                    ->get();

                // Mengambil array ID dari request atau menggunakan default
                $historyIds = $updatedReports->pluck('id')->toArray();

                // Menambahkan ID ke relasi newProgressReportHistories
                if (!empty($historyIds)) {
                    $dailyNotification->newProgressReportHistories()->attach($historyIds);
                }

                // Ambil data DailyNotification dengan eager loading yang lebih optimal
                $dailyNotification = DailyNotification::with([
                    'newProgressReportHistories.newProgressReport.newreport.projectType'
                ])->find($dailyNotification->id);

                if (!$dailyNotification) {
                    return response()->json(['message' => 'DailyNotification not found'], 404);
                }

                // Mengambil daftar nama dokumen berdasarkan ID
                $documentKindNames = NewProgressReportDocumentKind::pluck('name', 'id');

                // Mengelompokkan laporan berdasarkan jenis dokumen
                $documentview = $dailyNotification->newProgressReportHistories->groupBy(
                    fn($report) => $documentKindNames[$report->documentkind_id] ?? '📁 Dokumen Lainnya'
                );

                // Siapkan pesan untuk WhatsApp
                $message = "📢 *Laporan Ekspedisi Dokumen Terbaru!* 📢\n\n";
                $message .= "📂 *Daftar dokumen dapat diunduh melalui link berikut:*\n";

                // Variabel untuk melacak apakah ada laporan yang sesuai dengan projectname
                $hasMatchingProject = false;

                if ($documentview->isEmpty()) {
                    $message .= "⚠️ *Dokumen kosong. Tidak ada laporan ekspedisi dokumen terbaru.*\n\n";
                } else {
                    foreach ($documentview as $documentKind => $reports) {
                        $message .= "📌 *{$documentKind}*\n";
                        $message .= "━━━━━━━━━━━━━━━━━━━━━━\n";

                        foreach ($reports as $report) {
                            $projectTitle = $report->newProgressReport->newreport->projectType->title;
                            $vaultLink = $report->newProgressReport->newreport->projectType->vault_link ?? 'Belum dicantumkan';

                            if ($projectname == $projectTitle) {
                                $hasMatchingProject = true; // Tandai bahwa ada kecocokan
                                $message .= "🏗️ *Proyek:* {$projectTitle}\n";
                                $message .= "🌐 *Vault Link:* {$vaultLink}\n";
                                $message .= "📜 *No Dokumen:* {$report->nodokumen}\n";
                                $message .= "📄 *Nama Dokumen:* {$report->namadokumen}\n";
                                $message .= "🔄 *Revisi:* {$report->rev}\n";
                                $message .= "📑 *DCR:* {$report->dcr}\n";
                                $message .= "📌 *Status:* {$report->status}\n";
                                $message .= "━━━━━━━━━━━━━━━━━━━━━━\n";
                            }
                        }

                        $message .= "\n"; // Tambahkan spasi antar kategori
                    }
                }

                $message .= "🚀 *Silakan dikonfirmasi!* Terima kasih atas kerja sama dan dukungan Anda. 🙏😊";

                // Kirim pesan WhatsApp hanya jika ada laporan yang sesuai dengan projectname
                if ($hasMatchingProject) {
                    TelegramService::sendTeleMessage(['6281515814752'], $message);
                }

                // Selalu hapus DailyNotification setelah proses selesai
                $dailyNotification->newProgressReportHistories()->detach(); // Detach relasi terlebih dahulu
                $dailyNotification->delete(); // Hapus entri

                // Menyimpan unit yang berhasil diproses
                $successUnits[] = $unit;
            }
        }

        // Mengembalikan daftar unit yang berhasil terbuat
        return "Unit yang berhasil terbuat: " . implode(', ', $successUnits);
    }


    public function searchdokumenbywa(Request $request)
    {
        // Validasi input pencarian
        $request->validate([
            'query' => 'required|string|min:1',
        ]);

        // Ambil query dari input form
        $query = $request->input('query');

        // Lakukan pencarian berdasarkan nodokumen, namadokumen, level, atau drafter
        $results = NewProgressReport::with([
            'documentKind',
            'latestHistory.latestFile',
            'newreport.projectType'
        ])->where('nodokumen', 'LIKE', '%' . $query . '%')
            ->orWhere('namadokumen', 'LIKE', '%' . $query . '%')
            ->get();

        // Inisialisasi string untuk menampung hasil dalam bentuk teks
        $textResult = "";

        // Jika ada hasil pencarian, tambahkan header
        if ($results->count() > 0) {
            $textResult .= "🔍 *Hasil Pencarian untuk:* _" . $query . "_\n\n";
        }

        // Looping melalui hasil pencarian dan susun dalam format teks
        foreach ($results as $result) {
            $documentKind = $result->documentkind->name ?? "❌ *Jenis Dokumen*: Belum ada jenis dokumennya";
            $status = $result->getLatestRevAttribute()->status ?? $result->status ?? "❌ *Status*: Belum ada status";
            $revisiTerakhir = $result->getLatestRevAttribute()->rev ?? "❌ *Revisi Terakhir*: Belum ada";

            // Tambahkan detail setiap dokumen ke $textResult dengan lebih menarik
            $textResult .= "📄 *Dokumen No*: " . $result->nodokumen . "\n";
            $textResult .= "📋 *Nama Dokumen*: " . $result->namadokumen . "\n";
            $textResult .= "📊 *Level*: " . $result->level . "\n";
            $textResult .= "✏️ *Drafter*: " . $result->drafter . "\n";
            $textResult .= "✔️ *Checker*: " . $result->checker . "\n";
            $textResult .= "⏳ *Deadline Release*: " . $result->deadlinereleasedate . "\n";
            $textResult .= "📚 *Jenis Dokumen*: " . $documentKind . "\n";
            $textResult .= "✅ *Realisasi*: " . $result->realisasi . "\n";
            $textResult .= "📌 *Status*: " . $status . "\n";
            $textResult .= "🔄 *Revisi Terakhir*: " . $revisiTerakhir . "\n\n";

            $textResult .= "📋 *--- Dokumen ---*:\n\n";

            if ($result->latestHistory && $result->latestHistory->latestFile) {
                $fileId = $result->latestHistory->latestFile->id;
                $textResult .= "📎 *Ingin download File Revisi {$revisiTerakhir}*: ketik *Downloadfile_{$fileId}*\n\n";
            } else {
                $textResult .= "❌ *File*: Tidak ada file untuk revisi terakhir.\n\n";
            }
            $textResult .= "🏢 *Unit*: " . $result->newreport->unit . "\n";
            $textResult .= "🏗️ *Project*: " . $result->newreport->projectType->title . "\n";
            $textResult .= "----------------------------------\n\n"; // Garis pemisah antar dokumen
        }

        // Jika tidak ada hasil, kembalikan pesan "Tidak ada hasil"
        if (empty($textResult)) {
            $textResult = "⚠️ Tidak ada dokumen yang ditemukan untuk pencarian: *" . $query . "*";
        }

        // Kembalikan hasil pencarian dalam bentuk teks
        return response($textResult)->header('Content-Type', 'text/plain');
    }

    public function getproject(Request $request)
    {
        $projectTitle = $request->query('project');

        // Validasi parameter
        if (!$projectTitle) {
            return response()->json(['error' => 'Project title is required.'], 400);
        }

        // Ambil data workload berdasarkan project title
        $workloadData = Newprogressreport::getHoursProjectDatabyProject($projectTitle);

        return response()->json($workloadData);
    }

    //Fitur Baru
    public function downloadFormDraft(Request $request)
    {
        // Ambil semua data dari form kecuali token CSRF
        $formData = $request->except(['_token']);

        // Jika data kosong, beri peringatan
        if (empty($formData)) {
            return back()->with('error', 'Form masih kosong, tidak ada data untuk di-backup.');
        }

        // Kita bungkus data ke dalam collection untuk Excel
        // Kita gunakan array_values dan array_keys agar nama tabel/kolom yang berbeda tetap terbaca
        $dataExport = collect([$formData]);

        return Excel::download(
            new class ($dataExport) implements \Maatwebsite\Excel\Concerns\FromCollection, \Maatwebsite\Excel\Concerns\WithHeadings {
            private $data;
            public function __construct($data)
            {
                $this->data = $data; }
            public function collection()
            {
                return $this->data; }
            public function headings(): array
            {
                return array_keys($this->data->first());
            }
            },
            'backup_progres_dokumen_' . date('Ymd_His') . '.xlsx'
        );
    }

    /**
     * Fungsi Final Submit dari Form.
     * Menggunakan DB Transaction untuk memastikan data tersimpan aman.
     */
    public function submitFormFiks(Request $request)
    {
        try {
            DB::beginTransaction();

            // Simpan ke tabel utama Newprogressreport
            $newProgressReport = new Newprogressreport();
            $newProgressReport->newreport_id = $request->input('newreport_id');
            $newProgressReport->nodokumen = $request->input('nodokumen');
            $newProgressReport->namadokumen = $request->input('namadokumen');
            $newProgressReport->documentkind_id = $request->input('documentkind_id') ?? $request->input('jenisdokumen');

            // Kolom opsional yang mungkin berbeda di setiap form
            $newProgressReport->drafter = $request->input('drafter');
            $newProgressReport->checker = $request->input('checker');
            $newProgressReport->status = $request->input('status', 'RELEASED'); // Default fiks
            $newProgressReport->save();

            // Log System
            $newreport = Newreport::find($newProgressReport->newreport_id);
            $projectandvalue = Newreport::calculatelastpercentage();

            $newreport->systemLogs()->create([
                'message' => json_encode([
                    'message' => 'Data dikirim via Form Fiks',
                    'datasesudah' => [$newProgressReport],
                    'persentase' => $projectandvalue[0],
                ]),
                'level' => 'info',
                'user' => auth()->user()->name,
                'user_id' => auth()->user()->id,
                'aksi' => 'progresscreate_fiks',
            ]);

            DB::commit();
            return redirect()->route('newreports.index')->with('success', 'Data progres dokumen telah berhasil disimpan secara permanen.');

        } catch (Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyimpan data: ' . $e->getMessage())->withInput();
        }
    }



    public function downloadExcelDraft(Request $request)
    {
        $items = $request->input('items');
        $formatType = $request->input('format_type');

        // Log untuk debugging
        \Log::info('Download Excel Draft', [
            'total_items' => count($items ?? []),
            'format_type' => $formatType,
            'items' => $items
        ]);

        if (empty($items)) {
            return back()->with('error', 'Data tabel masih kosong.');
        }

        // Reset index agar urut 0, 1, 2...
        $items = array_values($items);

        $headers = [
            'formatprogress' => [
                'No',
                'Target Proyek',
                'No Dokumen',
                'Nama Dokumen',
                'Unit',
                'Paper',
                'Sheet',
                'Rev',
                'Rev.DD',
                'Tgl Drawing',
                'Tgl Release',
                'Drafter',
                'Checker',
                'Approval',
                'Welding',
                'Jenis Dokumen',
                'DCR',
                'Status',
                'File URL'
            ],
            'formatprogresskhusus' => [
                'No',
                'Target Proyek',
                'Unit',
                'No Dokumen',
                'Nama Dokumen',
                'Rev',
                'Level',
                'Drafter',
                'Checker',
                'Start Date',
                'Deadline',
                'Realisasi',
                'Status',
                'Paper',
                'Sheet'
            ],
            'formatrencana' => ['No', 'Target Proyek', 'Unit', 'No Dokumen', 'Nama Dokumen'],
            'formatupdatelink' => ['No', 'Target Proyek', 'No Dokumen', 'Unit', 'Rev', 'File URL']
        ];

        $header = $headers[$formatType] ?? ['Data'];

        $data = collect($items)->map(function ($item, $key) use ($formatType) {
            $no = $key + 1;

            // Ambil Nama Proyek
            $project = \App\Models\ProjectType::find($item['newreport_id'] ?? null);
            $projectLabel = $project ? $project->title : '-';

            if ($formatType === 'formatprogress') {
                return [
                    $no,
                    $projectLabel,
                    $item['nodokumen'] ?? '',
                    $item['namadokumen'] ?? '',
                    $item['unit'] ?? '',
                    $item['papersize'] ?? 'A4',
                    $item['sheet'] ?? '1',
                    $item['rev'] ?? '0',
                    $item['rev_dd'] ?? '',
                    $item['drawing_date'] ?? '',
                    $item['realisasidate'] ?? '',
                    $item['drafter'] ?? '',
                    $item['checker'] ?? '',
                    $item['approval'] ?? '',
                    $item['welding'] ?? '',
                    $item['jenisdokumen'] ?? '',
                    $item['dcr'] ?? '',
                    $item['status'] ?? 'RELEASED',
                    $item['fileurl'] ?? '',
                ];
            } elseif ($formatType === 'formatprogresskhusus') {
                return [
                    $no,
                    $projectLabel,
                    $item['unit'] ?? '',
                    $item['nodokumen'] ?? '',
                    $item['namadokumen'] ?? '',
                    $item['rev'] ?? '0',
                    $item['level'] ?? '',
                    $item['drafter'] ?? '',
                    $item['checker'] ?? '',
                    $item['startreleasedate'] ?? '',
                    $item['deadlinereleasedate'] ?? '',
                    $item['realisasidate'] ?? '',
                    $item['status'] ?? '',
                    $item['papersize'] ?? 'A4',
                    $item['sheet'] ?? '1',
                ];
            } elseif ($formatType === 'formatrencana') {
                return [$no, $projectLabel, $item['unit'] ?? '', $item['nodokumen'] ?? '', $item['namadokumen'] ?? ''];
            } elseif ($formatType === 'formatupdatelink') {
                return [$no, $projectLabel, $item['nodokumen'] ?? '', $item['unit'] ?? '', $item['rev'] ?? '', $item['fileurl'] ?? ''];
            }
            return array_merge([$no], array_values($item));
        });

        // Gabungkan header dengan data
        $finalContent = collect([$header])->concat($data);

        // PENTING: Jangan pakai dd() di sini!
        //dd($items);
        return Excel::download(
            new class ($finalContent) implements \Maatwebsite\Excel\Concerns\FromCollection, \Maatwebsite\Excel\Concerns\WithStyles {
            protected $data;

            public function __construct($data)
            {
                $this->data = $data;
            }

            public function collection()
            {
                return $this->data;
            }

            public function styles($sheet)
            {
                // Style header
                $sheet->getStyle('1')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'color' => ['rgb' => 'FFFFFF']
                    ],
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '007bff']
                    ]
                ]);

                // Auto-size kolom
                foreach (range('A', $sheet->getHighestColumn()) as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }

                return [];
            }
            },
            'Draft_Input_' . $formatType . '_' . date('Ymd_His') . '.xlsx'
        );
    }
    // Bagian Menampilkan Form (Controller)
    
    public function createDynamic()
    {
        // 1. Ambil data Proyek dengan relasi projectType
        $newreports = ProjectType::all();

        $units = NewprogressreportUnit::all();
        // 2. Ambil data Jenis Dokumen
        $documentKinds = NewProgressReportDocumentKind::orderBy('name')->get();

        // 3. Ambil data User untuk pilihan Drafter & Checker
        // Pastikan model User sudah di-import (use App\Models\User;)
        $users = \App\Models\User::whereNotNull('initial')
            ->orderBy('initial')
            ->get();

        return view('newreports.create_dynamic', compact('newreports', 'documentKinds', 'users', 'units'));
    }



    public function storeDynamic(Request $request)
    {
        $items = $request->input('items');
        $formatType = $request->input('format_type');

        if (empty($items)) {
            return response()->json(['error' => 'Data tidak boleh kosong.'], 422);
        }

        try {
            DB::beginTransaction();

            foreach ($items as $data) {
                $projectId = $data['newreport_id'] ?? null;
                $checker = is_numeric($item['checker']) ? User::find($item['checker'])->name : $item['checker'];

                if (empty($data['nodokumen']) || empty($projectId))
                    continue;

                $noDoc = strtoupper(trim($data['nodokumen']));

                // 1. Ambil nilai unit dari input (yang diisi di tabel dinamis)
                $unitValue = $data['unit'] ?? null;

                $saveData = [
                    'newreport_id' => $projectId,
                    'nodokumen' => $noDoc,
                    'namadokumen' => $data['namadokumen'] ?? null,
                    'unit' => $unitValue, // Menyimpan unit ke tabel utama
                    'drafter' => $data['drafter'] ?? null,
                    'checker' => $data['checker'] ?? null,
                    'approval' => $data['approval'] ?? null,
                    'welding' => $data['welding'] ?? null,
                    'drawing_date' => $data['drawing_date'] ?? null,
                    'papersize' => $data['papersize'] ?? 'A4',
                    'sheet' => $data['sheet'] ?? 1,
                    'startreleasedate' => $data['startreleasedate'] ?? null,
                    'deadlinereleasedate' => $data['deadlinereleasedate'] ?? null,
                    'realisasidate' => $data['realisasidate'] ?? null,
                    'dcr' => $data['dcr'] ?? null,
                    'status' => ($formatType === 'formatrencana') ? 'Terbuka' : ($data['status'] ?? 'RELEASED'),
                    'updated_at' => now(),
                ];

                if (!empty($data['realisasidate'])) {
                    $saveData['realisasi'] = date('d.m.Y', strtotime($data['realisasidate']));
                }

                // Upsert Logik
                $existing = DB::table('newprogressreports')
                    ->where('newreport_id', $projectId)
                    ->where('nodokumen', $noDoc)
                    ->first();

                if ($existing) {
                    DB::table('newprogressreports')->where('id', $existing->id)->update($saveData);
                    $currentId = $existing->id;
                } else {
                    $saveData['created_at'] = now();
                    $currentId = DB::table('newprogressreports')->insertGetId($saveData);
                }

                // 2. LOGIKA TAMBAHAN: Simpan/Update ke tabel newprogressreport_units
                // Gunakan updateOrInsert agar unit tersinkronisasi
                if (!empty($unitValue)) {
                    DB::table('newprogressreport_units')->updateOrInsert(
                        [
                            'newprogressreport_id' => $currentId,
                            'unit_name' => $unitValue 
                        ],
                        [
                            'updated_at' => now(),
                            'created_at' => now(),
                        ]
                    );
                }

                // Simpan ke History
                if ($formatType !== 'formatrencana') {
                    DB::table('newprogressreporthistorys')->insert([
                        'newprogressreport_id' => $currentId,
                        'nodokumen' => $noDoc,
                        'namadokumen' => $data['namadokumen'] ?? ($existing->namadokumen ?? null),
                        'unit' => $unitValue, // History juga mencatat unit
                        'rev' => $data['rev'] ?? '0',
                        'rev_dd' => $data['rev_dd'] ?? null,
                        'status' => $saveData['status'],
                        'drafter' => $data['drafter'] ?? null,
                        'checker' => $data['checker'] ?? null,
                        'realisasidate' => $data['realisasidate'] ?? now(),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }

            DB::commit();
            return response()->json(['success' => 'Data berhasil disinkronkan termasuk unit.']);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Gagal: ' . $e->getMessage()], 500);
        }
    }

}