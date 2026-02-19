<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Cache;
use App\Models\Newprogressreport;
use App\Models\NewProgressReportDocumentKind;
use App\Models\Wagroupnumber;
use App\Services\TelegramService;
use Carbon\Carbon; // Import Carbon class
use App\Models\Category;
use App\Models\Newreport;
use App\Models\ProjectType;
use App\Models\SystemLog;
use Illuminate\Http\Request;
use App\Imports\ColumnAImport;
use App\Exports\NewreportExport;
use Illuminate\Routing\Controller;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\NewreportExportDownload;
use App\Exports\NewreportExportDownloadMultipleUnit;
use App\Http\Controllers\FileController;
use App\Exports\NewreportDuplicateExport;
use App\Models\CollectFile;
use App\Models\Newprogressreporthistory;
use GuzzleHttp\Client;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\StreamedResponse;
use App\Models\NewProgressReportsLevel;

class NewreportController extends Controller
{
    protected $fileController;
    protected $logController;

    public function __construct(FileController $fileController, LogController $logController)
    {
        $this->fileController = $fileController;
        $this->logController = $logController;
    }

    public function indexlevel()
    {
        $selectedProjectId = request('project');

        $projects = ProjectType::where('is_active', true)->get();
        // Auto-select project dari query string
        if ($selectedProjectId && $projects->pluck('id')->contains($selectedProjectId)) {
            $projects = $projects->sortByDesc(fn($p) => $p->id == $selectedProjectId)->values();
        }

        return view('newreports.indexlevel.index', compact(
            'projects',
            'selectedProjectId'
        ));
    }

    public function indexlevelmonitor()
    {
        $selectedProjectId = request('project');

        $projects = ProjectType::where('is_active', true)->get();
        // Auto-select project dari query string
        if ($selectedProjectId && $projects->pluck('id')->contains($selectedProjectId)) {
            $projects = $projects->sortByDesc(fn($p) => $p->id == $selectedProjectId)->values();
        }

        return view('newreports.indexlevel.monitor', compact(
            'projects',
            'selectedProjectId'
        ));
    }

    public function indexlevelData($projectId)
    {
        $project = ProjectType::with([
            'newreports.newprogressreports.documentKind',
            'newreports.newprogressreports.levelKind'
        ])->findOrFail($projectId);

        $levels = NewProgressReportsLevel::all();
        $documentKinds = NewProgressReportDocumentKind::all();

        $stats = [];

        foreach ($project->newreports->pluck('newprogressreports')->flatten() as $pr) {
            $levelId = $pr->level_id;
            $kindId = $pr->documentkind_id ?? 0;
            $isReleased = ($pr->status === 'RELEASED');

            // Ambil tanggal, fallback ke null kalau tidak ada
            $startDate = $pr->startreleasedate ? \Carbon\Carbon::parse($pr->startreleasedate) : null;
            $deadlineDate = $pr->deadlinereleasedate ? \Carbon\Carbon::parse($pr->deadlinereleasedate) : null;

            if (!isset($stats[$levelId])) {
                $stats[$levelId] = [
                    'total' => 0,
                    'released' => 0,
                    'unreleased' => 0,
                    'kinds' => [],
                    'start_date' => null, // paling awal
                    'deadline_date' => null, // paling akhir
                    'kinds_detail' => [], // Tambahan untuk detail per jenis
                ];
            }

            $stats[$levelId]['total']++;
            if ($isReleased) {
                $stats[$levelId]['released']++;
            } else {
                $stats[$levelId]['unreleased']++;
            }
            $stats[$levelId]['kinds'][$kindId] = ($stats[$levelId]['kinds'][$kindId] ?? 0) + 1;

            // Update start date (paling awal)
            if ($startDate) {
                if (!$stats[$levelId]['start_date'] || $startDate->lessThan($stats[$levelId]['start_date'])) {
                    $stats[$levelId]['start_date'] = $startDate;
                }
            }

            // Update deadline date (paling akhir)
            if ($deadlineDate) {
                if (!$stats[$levelId]['deadline_date'] || $deadlineDate->greaterThan($stats[$levelId]['deadline_date'])) {
                    $stats[$levelId]['deadline_date'] = $deadlineDate;
                }
            }

            // === Deadline per jenis dokumen ===
            if (!isset($stats[$levelId]['kinds_detail'][$kindId])) {
                $stats[$levelId]['kinds_detail'][$kindId] = [
                    'total' => 0,
                    'released' => 0,
                    'unreleased' => 0,
                    'latest_deadline' => null,
                    'emoji' => '😐',
                    'emoji_class' => 'text-secondary',
                ];
            }
            $stats[$levelId]['kinds_detail'][$kindId]['total']++;
            if ($isReleased) {
                $stats[$levelId]['kinds_detail'][$kindId]['released']++;
            } else {
                $stats[$levelId]['kinds_detail'][$kindId]['unreleased']++;
            }
            if ($deadlineDate && (!$stats[$levelId]['kinds_detail'][$kindId]['latest_deadline'] ||
                $deadlineDate->greaterThan($stats[$levelId]['kinds_detail'][$kindId]['latest_deadline']))) {
                $stats[$levelId]['kinds_detail'][$kindId]['latest_deadline'] = $deadlineDate;
            }
        }

        // === Hitung emoticon per jenis dokumen ===
        $now = \Carbon\Carbon::now();
        foreach ($stats as $levelId => $levelStats) {
            foreach ($levelStats['kinds_detail'] as $kindId => $kindDetail) {
                $latestDeadline = $kindDetail['latest_deadline'];
                $total = $kindDetail['total'];
                $released = $kindDetail['released'];
                $unreleased = $kindDetail['unreleased']; // Pakai langsung unreleased

                $isAllReleased = $released === $total;
                $isOverdue = $latestDeadline && $latestDeadline->lessThan($now);
                $isNearDeadline = $latestDeadline && $latestDeadline->greaterThanOrEqualTo($now) &&
                    $latestDeadline->diffInDays($now) <= 7; // <= 7 hari

                $emoji = '😐'; // Default netral
                $emojiClass = 'text-secondary';

                if ($isAllReleased && !$isOverdue) {
                    $emoji = '😊'; // Selesai dan tidak telat
                    $emojiClass = 'text-success';
                } elseif ($isOverdue && $unreleased > 0) {
                    $emoji = '😡'; // Telat dan belum selesai semua
                    $emojiClass = 'text-danger';
                } elseif ($isNearDeadline && $unreleased > 0) {
                    $emoji = '😱'; // Mendekati deadline dan belum selesai
                    $emojiClass = 'text-warning';
                } elseif ($isAllReleased && $isOverdue) {
                    $emoji = '😐'; // Selesai tapi telat
                    $emojiClass = 'text-secondary';
                } else {
                    $emoji = '😐'; // Default
                    $emojiClass = 'text-secondary';
                }

                $stats[$levelId]['kinds_detail'][$kindId]['emoji'] = $emoji;
                $stats[$levelId]['kinds_detail'][$kindId]['emoji_class'] = $emojiClass;
            }
        }

        $levelCards = [];
        foreach ($levels as $level) {
            $s = $stats[$level->id] ?? [
                'total' => 0,
                'released' => 0,
                'unreleased' => 0,
                'kinds' => [],
                'start_date' => null,
                'deadline_date' => null,
                'kinds_detail' => [],
            ];

            $cardClass = match (true) {
                $s['total'] >= 200 => 'border-left-success',
                $s['total'] >= 100 => 'border-left-info',
                $s['total'] >= 50 => 'border-left-primary',
                $s['total'] > 0 => 'border-left-warning',
                default => 'border-left-secondary',
            };

            $percentage = $s['total'] > 0 ? round(($s['released'] / $s['total']) * 100, 1) : 0;
            $levelCards[] = [
                'level_id' => $level->id,
                'level_title' => $level->title,
                'total' => $s['total'],
                'released' => $s['released'],
                'unreleased' => $s['unreleased'],
                'percentage' => $percentage,
                'card_class' => $cardClass,
                'kinds' => $s['kinds'],
                'kinds_detail' => $s['kinds_detail'],
                'view_url' => url("/newreports/level/{$project->id}/{$level->id}"),
                'start_date' => $s['start_date']?->format('d M Y'),
                'deadline_date' => $s['deadline_date']?->format('d M Y'),
            ];
        }

        return response()->json([
            'project' => [
                'id' => $project->id,
                'title' => $project->title,
            ],
            'date' => now()->format('d F Y'),
            'levels' => $levelCards,
            'document_kinds' => $documentKinds->pluck('name', 'id')->toArray() + [0 => 'Belum Diketik Jenis'],
        ]);
    }


    public function showlevel($project_id, $level_id)
    {
        $project = ProjectType::find($project_id);
        $level = NewProgressReportsLevel::find($level_id);
        // 1. Ambil semua Newreport berdasarkan project_tye_id (asumsi kolomnya project_tye_id)
        $newreports = Newreport::where('proyek_type_id', $project_id)->get();
        // Jika hanya butuh ID-nya saja (lebih efisien), gunakan pluck
        $newreportIds = $newreports->pluck('id')->toArray();
        $user = auth()->user();
        // Fetch the report with eager-loaded relationships
        $newprogressreports = Newprogressreport::with([
            'children',
            'documentKind',
            'levelKind',
            'histories.documentKind',
            'latestHistory' => function ($query) {
                $query->with('latestFile');
            },
        ])->where('level_id', $level_id)->whereIn('newreport_id', $newreportIds)   // <-- diperbaiki di sini
            ->get();
        // Cache document kinds for 3 hours
        $jenisdokumen = Cache::remember('jenisdokumen', 1, fn() => NewProgressReportDocumentKind::all());
        // Initialize variables
        $progressReports = $newprogressreports;
        // Prepare status list and revisions
        $statuslist = ['All', 'RELEASED', 'UNRELEASED'];
        $revisiall = $this->prepareRevisions($progressReports, $statuslist);
        // Calculate progress metrics
        // Prepare view data
        return view('newreports.showlevel.show', [
            'progressReports' => $progressReports,
            'useronly' => $user,
            'jenisdokumen' => $jenisdokumen,
            'level' => $level,
            'project' => $project
        ]);
    }


    public function index()
    {

        // Cache selama 3 jam (180 menit)
        $listproject = Cache::remember('listproject', 180, function () {
            return ProjectType::all();
        });
        $allunitunderpe = Category::getlistCategoryMemberByName("unitunderpe");
        // Buat array untuk menyimpan singkatan unit
        $unitsingkatan = [];
        foreach ($allunitunderpe as $unit) {
            $unitsingkatan[$unit] = $this->fileController->singkatanUnit($unit);
        }
        $newreports = Newreport::with('newprogressreports.levelKind')
            ->orderBy('created_at', 'desc')
            ->get();

        [$newreports, $revisiall] = Newreport::indexnewreport($unitsingkatan, $listproject, $newreports);
        return view('newreports.index.index', compact('newreports', 'revisiall'));
    }

    public function indexperproject()
    {
        // Ambil daftar project untuk dropdown
        $projects = Cache::remember('listproject', 180, function () {
            return ProjectType::all();
        });
        $user = auth()->user();

        return view('newreports.effectiveindex.index', compact('projects', 'user'));
    }

    public function getDashboardData(Request $request)
    {
        $projectId = $request->input('project_id');
        $deadlineDate = $request->input('deadline_date');

        if (!$projectId) {
            return response()->json(['error' => 'Project ID is required'], 400);
        }

        if ($deadlineDate && !Carbon::hasFormat($deadlineDate, 'Y-m-d')) {
            return response()->json(['error' => 'Invalid deadline date format'], 400);
        }

        $newreports = Newreport::with([
            'newprogressreports.newprogressreporthistory',
            'projectType:id,title',   // id & title saja supaya ringan
            'unitType.progressDocumentKinds',           // id & name saja supaya ringan
        ])
            ->where('proyek_type_id', $projectId)
            ->orderBy('created_at', 'desc')
            ->get();

        $data = [];
        $no = 1;
        $totalDocs = 0;
        $totalReleasedDocs = 0;
        $totalNullDocs = 0;

        foreach ($newreports as $newreport) {
            // Filter sesuai deadlineDate
            $progressReports = $newreport->newprogressreports->filter(function ($progressReport) use ($deadlineDate) {
                if (!$deadlineDate) {
                    return true;
                }

                $deadline = Carbon::parse($deadlineDate);

                // Jika punya history
                if ($progressReport->newprogressreporthistory && $progressReport->newprogressreporthistory->isNotEmpty()) {
                    foreach ($progressReport->newprogressreporthistory as $history) {
                        if ($history->realisasidate && Carbon::parse($history->realisasidate)->lte($deadline)) {
                            return true;
                        }
                    }
                    return false; // Tidak ada history yang deadline-nya memenuhi
                }

                // Kalau tidak punya history
                if ($progressReport->status != 'RELEASED') {
                    return true; // masukkan meskipun tanggal tidak dicek
                }

                if ($progressReport->status === 'RELEASED') {
                    // Jika realisasidate tidak diisi, tetap masukkan
                    if (!$progressReport->realisasidate) {
                        return true;
                    }
                    return Carbon::parse($progressReport->realisasidate)->lte($deadline);
                }

                return false; // selain status null atau RELEASED, tidak dimasukkan
            });

            $releasedCount = $progressReports->where('status', 'RELEASED')->count();
            $nullCount = $progressReports->where('status', '!=', 'RELEASED')->count();
            $totalDocsCount = $progressReports->count();
            $percentage = $totalDocsCount > 0 ? ($releasedCount / $totalDocsCount) * 100 : 0;

            // Accumulate totals
            $totalDocs += $totalDocsCount;
            $totalReleasedDocs += $releasedCount;
            $totalNullDocs += $nullCount;

            $data[] = [
                'id' => $newreport->id,
                'no' => $no++,
                'unit_type'          => optional($newreport->unitType)->name ?? '-',      // atau $newreport->unitRelation->name
                'project_type'  => optional($newreport->projectType)->title ?? '-',
                'progressDocumentKinds' => $newreport->unitType?->progressDocumentKinds?->pluck('name')->toArray() ?? [],
                'percentage' => number_format($percentage, 2) . '%',
                'released_docs' => $releasedCount,
                'null_docs' => $nullCount,
                'total_docs' => $totalDocsCount,
                'created_at' => $newreport->created_at->format('Y-m-d'),
            ];
        }

        // Add totals to the response
        return response()->json([
            'data' => $data,
            'total_docs' => $totalDocs,
            'total_released_docs' => $totalReleasedDocs,
            'total_null_docs' => $totalNullDocs,
        ]);
    }

    public function indexslideshow()
    {

        // Cache selama 3 jam (180 menit)
        // Cache selama 3 jam (180 menit)
        $listproject = Cache::remember('listprojectkhusus', 180, function () {
            return ProjectType::whereIn('title', ['KCI', 'Retrofit', '1164 PPCW BM 54 TON', '50 Locomotive Platform UGL'])->get();
        });

        $allunitunderpe = Category::getlistCategoryMemberByName("unitunderpe");

        // Buat array untuk menyimpan singkatan unit
        $unitsingkatan = [];
        foreach ($allunitunderpe as $unit) {
            $unitsingkatan[$unit] = $this->fileController->singkatanUnit($unit);
        }
        $newreports = Newreport::with('newprogressreports')
            ->orderBy('created_at', 'desc')
            ->get();
        [$newreports, $revisiall] = Newreport::indexnewreport($unitsingkatan, $listproject, $newreports);
        return view('newreports.index.indexslideshow', compact('newreports', 'revisiall'));
    }

    public function calculatelastpercentage()
    {
        $projectandvalue = Newreport::calculatelastpercentage();
        return $projectandvalue;
    }

    public function indexlogpercentage()
    {
        $logs = Newreport::historyPercentage()->sortByDesc('created_at');
        return view('newreports.indexlogpercentage', compact('logs'));
    }

    public function getColumnA()
    {
        // Path ke file Excel
        $filePath = public_path('daftaranggota/daftaranggota.xlsx');
        // Menggunakan import class untuk membaca file Excel
        $data = Excel::toCollection(new ColumnAImport, $filePath);
        // Memastikan sheet tidak kosong
        if ($data->isEmpty()) {
            return response()->json(['error' => 'File Excel kosong atau tidak valid'], 400);
        }
        // Mengambil sheet pertama
        $sheet = $data->first();
        // Mengambil nilai dari kolom A
        $columnA = $sheet->pluck('0')->toArray();
        // Mengembalikan sebagai respons JSON
        return $columnA;
    }

    public function create()
    {
        return view('newreports.create');
    }

    public function store(Request $request)
    {
        Newreport::create($request->all());
        return redirect()->route('newreports.index')->with('success', 'New report created successfully');
    }


    public function doubledetector($id)
    {
        $newreport = Newreport::select('id', 'unit')->find($id);
        $duplicates = $newreport->doubledetector();
        // Kembalikan hasil sebagai JSON
        if (!empty($duplicates)) {
            return response()->json(['duplicates' => $duplicates], 200);
        } else {
            return response()->json(['message' => 'No duplicate nodokumen found'], 200);
        }
    }

    public function destroydian($id)
    {
        $newreport = Newreport::select('id', 'unit')->find($id);

        if ($newreport) {
            $hasil = $newreport->destroydian();
            return response()->json([
                'informasi' => $hasil
            ], 200);
        }
    }

    public function downloadprogress(Request $request, $id)
    {
        // Validate input dates if necessary
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        // Ambil data Newreport berdasarkan ID
        $newreport = Newreport::select('id', 'unit')->find($id);
        $unit = $newreport->unit;
        $project = $newreport->proyek_type;

        // Ambil data progress dari Newreport
        $progressData = $newreport->getProgressData();

        // Ambil tanggal awal dan akhir dari request
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        // Hitung data mingguan sesuai dengan rentang tanggal yang dipilih
        $data = $newreport->calculateWeeklyData($progressData['progressReports'], $startDate, $endDate);
        $weekData = $newreport->calculateWeeklyPercentage($progressData['progressReports'], $startDate, $endDate);

        // Buat objek NewreportExport
        // Combine data for export
        $informasi = [];
        $exportData = [];
        foreach ($data as $week => $item) {
            $exportData[] = [
                'Week' => $week,
                'Start Date' => $item['start'],
                'End Date' => $item['end'],
                'Total Revisions (Plan)' => $weekData[$week]['value'],
                'Total Revisions (Realisasi)' => $item['nilai'],
                'Total Percentage (Plan)' => $weekData[$week]['percentage'],
                'Total Percentage (Realisasi)' => $item['nilaipresentase'],
            ];
        }
        $informasi = [];
        $informasi[] = [
            'Unit' => $unit,
            'Project' => $project,
            'Exporteddata' => $exportData,
        ];
        // Buat objek NewreportExport
        $export = new NewreportExport($informasi);

        // Tentukan nama file Excel
        $fileName = $newreport->unit . "_" . $newreport->proyek_type . "_" . now()->timestamp . '.xlsx';

        // StreamedResponse untuk langsung download
        return Excel::download($export, $fileName);
    }

    public function downloadprogressbyproject(Request $request, $project)
    {
        // Validate input dates
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        // Retrieve start and end dates from request
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        // Fetch all Newreport data with the given project type
        $informasi = Newreport::downloadprogressbyproject($project, $startDate, $endDate);

        // Create NewreportExport object
        $export = new NewreportExport($informasi);
        // Define the Excel file name
        $fileName = 'All_Units_Report_' . now()->timestamp . '.xlsx';

        // Return StreamedResponse to directly download the file
        return Excel::download($export, $fileName);
    }

    public function viewbyprojectprogress(Request $request, $project)
    {
        // Validate input dates
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'project' => 'required|string',
        ]);

        // Retrieve start and end dates from request
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $listproject = [$project];
        $allunitunderpe = Category::getlistCategoryMemberByName("unitunderpe");
        // Buat array untuk menyimpan singkatan unit
        $unitsingkatan = [];
        foreach ($allunitunderpe as $unit) {
            $unitsingkatan[$unit] = $this->fileController->singkatanUnit($unit);
        }
        [$newreports, $revisiall] = Newreport::byprojectprogress($unitsingkatan, $listproject, $startDate, $endDate);
        return view('newreports.viewpercentage', compact('newreports', 'revisiall', 'startDate', 'endDate'));
    }

    public function downloadduplicatebyproject(Request $request, $project)
    {

        // Fetch all Newreport data with the given project type
        $informasi = Newreport::downloaddoubledetector($project);

        // Create NewreportExport object
        $export = new NewreportDuplicateExport($informasi);
        // Define the Excel file name
        $fileName = 'All_Units_Duplicate_Document_Report_' . now()->timestamp . '.xlsx';

        // Return StreamedResponse to directly download the file
        return Excel::download($export, $fileName);
    }


    public function downloadlaporan(Request $request, $id)
    {
        $newreport = Newreport::with([
            'projectType',
            'newprogressreports.histories',
            'newprogressreports.levelKind',
            'newprogressreports.documentkind'
        ])->find($id);

        $proyek_type = $newreport->projectType;
        $unitname = $newreport->unit;

        // Retrieve the related progress reports
        $progressreports = $newreport->newprogressreports;
        foreach ($progressreports as $result) {

            $lastHistory = $result->histories->last(); // simpan sekali

            $result->revisiTerakhir = $result->getLatestRevAttribute()->rev ?? "Tidak tercantum";
            $result->kindofdocument = $result->documentkind->name ?? "Tidak tercantum";
            $result->level = $result->levelKind->title ?? "";
            $result->projecttype = $proyek_type->title ?? "";
            $result->unit = $unitname;

            // Jika nilai utama ada → pakai itu, jika tidak → pakai dari history
            $result->drafter = $result->drafter
                ?? optional($lastHistory)->drafter
                ?? "Tidak tercantum";

            $result->namadokumen = $result->namadokumen
                ?? optional($lastHistory)->namadokumen
                ?? "Tidak tercantum";

            $result->checker = $result->checker
                ?? optional($lastHistory)->checker
                ?? "Tidak tercantum";
        }


        // Convert Collection -> Array supaya array_map() di export bisa jalan
        $progressreports = $progressreports->all();

        // Create a new export instance with the progress reports
        $export = new NewreportExportDownload($progressreports);

        // Define the Excel file name
        $fileName = $unitname . "_" . $proyek_type->title . "_" . now()->timestamp . '.xlsx';

        return Excel::download($export, $fileName);
    }

    public function downloadlaporanall(Request $request, $project)
    {
        // Ambil semua newreport berdasarkan proyek dengan eager loading
        $newreports = Newreport::with([
            'projectType',
            'newprogressreports.histories',
            'newprogressreports.levelKind',
            'newprogressreports.documentkind'
        ])->where('proyek_type_id', $project)->get();

        // Jika tidak ada data, kembalikan response error
        if ($newreports->isEmpty()) {
            return back()->with('error', 'Data tidak ditemukan');
        }

        $allProgressReports = [];

        // Looping setiap newreport untuk mengumpulkan progress reports
        foreach ($newreports as $newreport) {
            $proyek_type = $newreport->projectType->title ?? "Tidak tercantum";
            $unitname = $newreport->unit ?? "Tidak tercantum";

            foreach ($newreport->newprogressreports as $result) {
                $result->revisiTerakhir = $result->getLatestRevAttribute()->rev ?? "Tidak tercantum";
                $result->kindofdocument = $result->documentkind->name ?? "Tidak tercantum";
                $result->level = $result->levelKind->title ?? "Tidak tercantum";
                $result->projecttype = $proyek_type;
                $result->unit = $unitname;

                // Masukkan ke array
                $allProgressReports[] = $result;
            }
        }

        // Jika tidak ada progress reports, kembalikan response error
        if (empty($allProgressReports)) {
            return back()->with('error', 'Tidak ada laporan progres untuk proyek ini');
        }

        // Buat instance export
        $export = new NewreportExportDownload($allProgressReports);

        // Nama file Excel
        $fileName = 'Laporan_Proyek_' . $project . "_" . now()->timestamp . '.xlsx';

        // Download file Excel
        return Excel::download($export, $fileName);
    }

    public function downloadlaporanallrevnol(Request $request, $project)
    {
        // Validasi input tanggal
        $request->validate([
            'cutoff_date' => 'required|date',
        ]);

        // Konversi tanggal ke format yang sesuai
        $cutoffDate = \Carbon\Carbon::parse($request->cutoff_date)->endOfDay();

        // Ambil semua newreport berdasarkan proyek dengan eager loading dan filter created_at
        $newreports = Newreport::with([
            'projectType',
            'newprogressreports.histories',
            'newprogressreports.levelKind',
            'newprogressreports.documentkind'
        ])
            ->where('proyek_type_id', $project)
            ->where('created_at', '<=', $cutoffDate)
            ->get();

        // Jika tidak ada data, kembalikan response error
        if ($newreports->isEmpty()) {
            return back()->with('error', 'Data tidak ditemukan');
        }

        // Grup progress reports berdasarkan unit
        $groupedProgressReports = [];

        foreach ($newreports as $newreport) {
            $proyek_type = $newreport->projectType->title ?? "Tidak tercantum";
            $unitname = $newreport->unit ?? "Tidak tercantum";

            foreach ($newreport->newprogressreports as $result) {
                if ($result->histories->isNotEmpty()) {
                    $history = $result->histories->first();
                    if ($history->rev == "0" && $history->realisasidate <= $cutoffDate) {
                        $history->revisiTerakhir = $history->rev ?? "Tidak tercantum";
                        $history->kindofdocument = $result->documentkind->name ?? "Tidak tercantum";
                        $history->level = $result->levelKind->title ?? "Tidak tercantum";
                        $history->projecttype = $proyek_type;
                        $history->unit = $unitname;
                        $groupedProgressReports[$unitname][] = $history;
                    }
                }
            }
        }

        // Jika tidak ada progress reports, kembalikan response error
        if (empty($groupedProgressReports)) {
            return back()->with('error', 'Tidak ada laporan progres untuk proyek ini');
        }

        // Nama file Excel
        $fileName = 'Laporan_Proyek_' . $project . "_" . now()->timestamp . '.xlsx';

        // Buat instance export dengan data yang dikelompokkan
        $export = new NewreportExportDownloadMultipleUnit($groupedProgressReports);

        // Download file Excel
        return Excel::download($export, $fileName);
    }

    public function edit(Newreport $newreport)
    {
        return view('newreports.edit', compact('newreport'));
    }

    public function update(Request $request, Newreport $newreport)
    {
        $newreport->update($request->all());
        return redirect()->route('newreports.index')->with('success', 'New report updated successfully');
    }

    public function destroy(Newreport $newreport)
    {
        $newreport->delete();
        return redirect()->route('newreports.index')->with('success', 'New report deleted successfully');
    }

    public function showlog($newreport, $logid)
    {
        $log = SystemLog::findOrFail($logid);
        return view('newreports.log', compact('log'));
    }

    public function show($id)
    {
        // Load authenticated user
        $user = auth()->user();

        // Fetch the report with eager-loaded relationships
        $newreport = $this->fetchNewReport($id);

        // Cache document kinds for 3 hours
        $jenisdokumen = Cache::remember('jenisdokumen', 1, fn() => NewProgressReportDocumentKind::all());

        // Initialize variables
        $progressReports = $newreport->newprogressreports;
        $generasi = $this->processProgressReports($progressReports, $newreport, $user, $id);

        // Calculate additional data
        $progressData = $newreport->getProgressData();
        $releaseinfo = $newreport->releasecount();
        $duplicates = $newreport->doubledetector();
        $levelStatusData = $newreport->calculateLevelStatusData($progressData['progressReports']);
        $percentageData = $newreport->calculatePercentageData($levelStatusData['datalevel'], $levelStatusData['datastatus']);
        $data = $newreport->calculateWeeklyData($progressData['progressReports'], '02-01-2023', '02-01-2025');
        $weekData = $newreport->calculateWeeklyPercentage($progressData['progressReports'], '02-01-2023', '02-01-2025');

        // Prepare status list and revisions
        $statuslist = ['All', 'RELEASED', 'UNRELEASED'];
        $revisiall = $this->prepareRevisions($progressReports, $statuslist);

        // Calculate progress metrics
        $progressPercentageFormatted = number_format($releaseinfo['progresspercentage'], 2);
        $this->setProgressMetrics($newreport, $user, $progressPercentageFormatted, $releaseinfo);

        // Prepare view data
        return view('newreports.show.show', [
            'newreport_id' => $id,
            'revisiall' => $revisiall,
            'newreport' => $newreport,
            'progressReports' => $progressReports,
            'datastatus' => $levelStatusData['datastatus'],
            'datalevel' => $levelStatusData['datalevel'],
            'percentageLevel' => $percentageData['percentageLevel'],
            'percentageStatus' => $percentageData['percentageStatus'],
            'indukan' => $progressData['indukan'],
            'listprogressnodokumenencode' => $progressData['listprogressnodokumen'],
            'listanggota' => $this->getColumnA(),
            'data' => $data,
            'weekData' => $weekData,
            'duplicates' => $duplicates,
            'countrelease' => $releaseinfo['countrelease'],
            'countunrelease' => $releaseinfo['countunrelease'],
            'progresspercentage' => $releaseinfo['progresspercentage'],
            'useronly' => $user,
            'generasi' => $generasi,
            'jenisdokumen' => $jenisdokumen,
        ]);
    }

    /**
     * Fetch Newreport with eager-loaded relationships.
     */
    /**
     * Fetch Newreport with eager-loaded relationships.
     */
    private function fetchNewReport($id): Newreport
    {
        return Newreport::with([
            'projectType',
            'newprogressreports' => function ($query) {
                $query->orderBy('realisasidate', 'desc');
            },
            'newprogressreports.children',
            'newprogressreports.documentKind',
            'newprogressreports.histories.documentKind',
            'newprogressreports.latestHistory' => function ($query) {
                $query->with('latestFile');
            },
            'systemLogs' => fn($query) => $query->orderBy('created_at', 'desc'),
        ])
            ->findOrFail($id);
    }


    /**
     * Process progress reports based on user role and session.
     */
    private function processProgressReports(Collection $progressReports, Newreport $newreport, $user, $id): array
    {
        $generasi = [];
        $isRestrictedRole = in_array($user->rule, [
            'QC FAB',
            'QC FIN',
            'QC INC',
            'Fabrikasi',
            'PPC',
            'QC Banyuwangi',
            'Pabrik Banyuwangi',
        ]);

        foreach ($progressReports as $progressReport) {
            $progressReport->newreport_id = $id;
            $this->setProgressReportStatus($progressReport, $newreport, $isRestrictedRole);
            $this->processProgressReportHistory($progressReport);
            $generasi[$progressReport->id] = [
                'childreen' => $progressReport->children,
                'count' => $progressReport->children->count(),
            ];
        }

        return $generasi;
    }

    /**
     * Set progress report status based on conditions.
     */
    private function setProgressReportStatus($progressReport, Newreport $newreport, bool $isRestrictedRole): void
    {
        $internalOn = Session::get('internalon', false);
        $progressReport->statusterbaru = $isRestrictedRole && !$internalOn ? ($progressReport->status ?? '') : ($progressReport->status ?? '');
    }

    /**
     * Process progress report history data.
     */
    private function processProgressReportHistory($progressReport): void
    {
        if (!$progressReport->histories->isEmpty()) {
            $progressReport->latestRev = $progressReport->latest_rev;
            if ($latestRev = $progressReport->getLatestRevAttribute()) {
                $progressReport->realisasi = $latestRev->realisasi;
                if ($latestRev->status === 'RELEASED') {
                    $progressReport->status = $latestRev->status;
                }
                $progressReport->namadokumen = $latestRev->namadokumen;
                $progressReport->rev = $latestRev->rev;
            }
        }
    }

    /**
     * Prepare revisions for status list.
     */
    private function prepareRevisions(Collection $progressReports, array $statuslist): array
    {
        $revisiall = [];
        foreach ($statuslist as $status) {
            $key = str_replace(' ', '_', $status);
            $revisiall[$key]['progressReports'] = $status === 'UNRELEASED'
                ? $progressReports->where('status', '!=', 'RELEASED')->all()
                : $progressReports->where('status', $status)->all();
        }
        $revisiall['All']['progressReports'] = $progressReports;

        return $revisiall;
    }

    /**
     * Set progress metrics for the report.
     */
    private function setProgressMetrics(Newreport $newreport, $user, float $progressPercentageFormatted, array $releaseinfo): void
    {
        $isRestrictedRole = in_array($user->rule, [
            'QC FAB',
            'QC FIN',
            'QC INC',
            'Fabrikasi',
            'PPC',
            'QC Banyuwangi',
            'Pabrik Banyuwangi',
        ]);
        $internalOn = Session::get('internalon', false);

        if ($isRestrictedRole && !$internalOn) {
            $newreport->nilaipersentase = $progressPercentageFormatted == 0 ? '-' : $progressPercentageFormatted . '%';
            $newreport->release = $releaseinfo['countrelease'];
            $newreport->unrelease = $releaseinfo['countunrelease'];
        } else {
            $newreport->nilaipersentase = $progressPercentageFormatted == 0 ? '-' : $progressPercentageFormatted . '%';
            $newreport->release = $releaseinfo['countrelease'];
            $newreport->unrelease = $releaseinfo['countunrelease'];
        }
    }



    public function showrev($idprogress, $id)
    {
        // Temukan report berdasarkan ID dengan eager loading pada relasi 'newprogressreporthistory'
        $newprogressreport = Newprogressreport::with('newprogressreporthistory')->findOrFail($id);

        // Mendapatkan user yang sedang login
        $userdef = auth()->user();

        // Mendapatkan data revisi yang terkait dengan report tersebut
        $newreporthistorys = $newprogressreport->newprogressreporthistory->sortByDesc(function ($history) {
            if ($history->rev === '0') return -1; // jadikan '0' paling kecil
            return ord(strtolower($history->rev)); // ASCII dari 'a' = 97, 'b' = 98, ...
        })->take(3) // ambil hanya 3 yang terbaru
            ->values(); // reset indeks

        // Kirim data ke view
        return view('newreports.newprogressreporthistory', [
            'idprogress' => $idprogress,
            'newprogressreport' => $newprogressreport,
            'newreporthistorys' => $newreporthistorys,
        ]);
    }

    public function updateDocumentNumber(Request $request)
    {
        // Validasi input
        $request->validate([
            'nodokumen' => 'required|string|max:255',
        ]);

        // Perbarui data nodokumen pada newprogressreport
        $nodokumen = $request->input('nodokumen');
        $nodokumenlama = $request->input('nodokumenlama');
        $newreport_id = $request->input('newreport_id');


        // Cari data newprogressreport dengan history-nya
        $newprogressreport = Newprogressreport::with('newprogressreporthistory')->where('newreport_id', $newreport_id)->where('nodokumen', $nodokumenlama)->first();

        // Periksa apakah data ditemukan
        if (!$newprogressreport) {
            return response()->json([
                'status' => 'error',
                'title' => 'Gagal!',
                'message' => 'Data tidak ditemukan' . $nodokumen . " " . $newreport_id,
            ], 404);
        }


        $newprogressreport->nodokumen = $nodokumen;
        $newprogressreport->save();

        // Perbarui data pada semua history terkait jika ada
        $updatedRows = 0;
        if ($newprogressreport->newprogressreporthistory()->exists()) {
            $updatedRows = $newprogressreport->newprogressreporthistory()->update(['nodokumen' => $nodokumen]);
        }

        // Berikan respons berdasarkan hasil update
        if ($updatedRows > 0) {
            return response()->json([
                'status' => 'success',
                'title' => 'Berhasil!',
                'message' => 'No dokumen berhasil diperbarui.'
            ]);
        } elseif ($updatedRows === 0 && $newprogressreport->wasChanged('nodokumen')) {
            return response()->json([
                'status' => 'success',
                'title' => 'Berhasil!',
                'message' => 'No dokumen pada laporan utama berhasil diperbarui.'
            ]);
        } else {
            return response()->json([
                'status' => 'warning',
                'title' => 'Perhatian!',
                'message' => 'Tidak ada data yang diperbarui.'
            ]);
        }
    }

    public function target(Request $request)
    {
        $availabledocumentname = NewProgressReportDocumentKind::pluck('name', 'id');
        $projectsData = [];

        // Cache selama 3 jam (180 menit)
        $proyek_types = Cache::remember('proyek_types', 180, function () {
            return ProjectType::all();
        });

        foreach ($proyek_types as $proyek_type) {
            $projectsData[$proyek_type->title] = [];
        }

        $project = $request->projectName ?? "";
        $download = $request->download ?? 'false';
        $start_date = $request->start_date ?? null;
        $end_date = $request->end_date ?? null;

        // Compact hanya jika variabel ada
        $compactData = compact('projectsData', 'project', 'download');

        if ($start_date) {
            $compactData['start_date'] = $start_date;
        } else {
            $compactData['start_date'] = null;
        }

        if ($end_date) {
            $compactData['end_date'] = $end_date;
        } else {
            $compactData['end_date'] = null;
        }

        return view('newreports.schedule', $compactData);
    }

    public function targetslideshow(Request $request)
    {
        $availabledocumentname = NewProgressReportDocumentKind::pluck('name', 'id');
        $projectsData = [];

        // Cache selama 3 jam (180 menit)
        $proyek_types = Cache::remember('proyek_types', 180, function () {
            return ProjectType::all();
        });

        foreach ($proyek_types as $proyek_type) {
            $projectsData[$proyek_type->title] = [];
        }

        $project = $request->projectName ?? "";
        $download = $request->download ?? 'false';
        $start_date = $request->start_date ?? null;
        $end_date = $request->end_date ?? null;

        // Compact hanya jika variabel ada
        $compactData = compact('projectsData', 'project', 'download');

        if ($start_date) {
            $compactData['start_date'] = $start_date;
        } else {
            $compactData['start_date'] = null;
        }

        if ($end_date) {
            $compactData['end_date'] = $end_date;
        } else {
            $compactData['end_date'] = null;
        }

        return view('newreports.schedule_slideshow', $compactData);
    }

    public function getProjectDataonehour(Request $request)
    {
        $project = $request->projectName;
        $end_date = $request->end_date;

        // Buat key cache unik berdasarkan nama project dan rentang tanggal
        $cacheKey = "project_data_{$project}_{$end_date}";

        $result = Cache::remember($cacheKey, now()->addMinutes(60), function () use ($project, $end_date) {
            return Newprogressreport::getProjectDataGantChart($project, $end_date);
        });

        return response()->json($result);
    }

    public function getProjectDatatenminutes(Request $request)
    {
        $project = $request->projectName;
        $end_date = $request->end_date;

        // Buat key cache unik berdasarkan nama project dan rentang tanggal
        $cacheKey = "project_data_{$project}_{$end_date}";

        $result = Cache::remember($cacheKey, now()->addMinutes(10), function () use ($project, $end_date) {
            return Newprogressreport::getProjectDataGantChart($project, $end_date);
        });

        return response()->json($result);
    }


    public function getProjectData(Request $request)
    {

        $end_date = $request->end_date;
        $project = $request->projectName;
        $result = Newprogressreport::getProjectDataGantChart($project, $end_date);
        return response()->json($result);
    }

    public function jamorang(Request $request)
    {
        $availabledocumentname = NewProgressReportDocumentKind::pluck('name', 'id');
        $projectsData = [];
        // Cache selama 3 jam (180 menit)
        $proyek_types = Cache::remember('proyek_types', 180, function () {
            return ProjectType::all();
        });
        foreach ($proyek_types as $proyek_type) {
            $projectsData[$proyek_type->title] = [];
        }

        $project = $request->projectName ?? "";
        $download = $request->download ?? 'false';

        return view('newreports.jo', compact('projectsData', 'project', 'download'));
    }

    public function getHoursProjectData(Request $request)
    {
        $year = $request->year;
        $result = Newprogressreport::getHoursProjectData($year);
        return response()->json($result);
    }


    public function downloadChart(Request $request)
    {


        $availabledocumentname = NewProgressReportDocumentKind::pluck('name', 'id');
        $projectsData = [];
        // Cache selama 3 jam (180 menit)
        $proyek_types = Cache::remember('proyek_types', 180, function () {
            return ProjectType::all();
        });
        foreach ($proyek_types as $proyek_type) {
            $projectsData[$proyek_type->title] = [];
        }



        $project = $request->projectName ?? "";
        $download = $request->download ?? 'false';

        return view('newreports.gantt_chart', compact('projectsData', 'project', 'download'));
    }

    public function notifprojectmingguan()
    {

        $allunitunderpe = ['Welding Technology', 'Preparation & Support', 'Shop Drawing', 'Mechanical Engineering System', 'Desain Bogie & Wagon', 'Desain Interior', 'Sistem Mekanik', 'Desain Elektrik', 'Teknologi Proses', 'Product Engineering', 'Quality Engineering', 'Electrical Engineering System', 'Desain Carbody'];

        // Daftar proyek yang ingin dimonitor
        $selectedProjects = [
            'KCI',
            'Retrofit',
            '1164 PPCW BM 54 TON',
            '48 Unit Bogie Train Merk F PT KAI',
            'PENGEMBANGAN GB BOTTOM DUMP 50 TON',
            '100 Unit Bogie TB1014',
            'Perbaikan K102427 Eks Temperan Taksaka',
            '450 Unit 40ft UGL',
            '50 Locomotive Platform UGL',
        ];

        // Ambil data laporan harian
        $dailyProgressReportDatas = collect(Newreport::dailyProgressReport($allunitunderpe, $selectedProjects));

        // Format tanggal saat ini
        $currentDate = Carbon::now()->format('d-m-Y');

        foreach ($allunitunderpe as $unit) {
            $progressData = $dailyProgressReportDatas->get($unit, []);

            if (empty($progressData)) {
                continue; // Jika tidak ada data untuk unit ini, lewati
            }

            // Buat pesan laporan
            $pesan = "📊 *Laporan Progress Mingguan*\n";
            $pesan .= "Unit: *$unit*\n";
            $pesan .= "----------------------------------\n";

            foreach ($progressData as $projectName => $progress) {
                $total = $progress['released'] + $progress['unreleased'];
                $percentage = $total > 0 ? round(($progress['released'] / $total) * 100, 2) : 0;

                $pesan .= "🔹 *$projectName* \n";
                $pesan .= "📊 Presentase Realtime: *{$percentage}%*\n";
                $pesan .= "----------------------------------\n";
            }

            if ($unit == "Desain Interior") {
                $unit = "Desain Mekanik & Interior";
            } else if ($unit == "Sistem Mekanik") {
                $unit = "Desain Mekanik & Interior";
            } else if ($unit == "MTPR") {
                $unit = "RAMS";
            }



            // Kirim notifikasi ke WhatsApp Group yang sesuai
            TelegramService::ujisendunit($unit, $pesan);
        }

        return response()->json(['message' => 'Notifikasi harian telah dikirim'], 200);
    }

    public function lastpdffile($idprogress, $id)
    {
        // Temukan report berdasarkan ID dengan eager loading pada relasi 'newprogressreporthistory'
        $result = Newprogressreport::with([
            'documentKind',
            'latestHistory.latestFile',
            'newreport.projectType'
        ])->find($id);

        if (!$result) {
            return redirect()->back()->with('error', 'Data tidak ditemukan.');
        }

        $fileId = null;
        if ($result->latestHistory && $result->latestHistory->latestFile) {
            $fileId = $result->latestHistory->latestFile->id;
            $last_rev = $result->latestHistory->rev;
        }

        if (!$fileId) {
            return redirect()->back()->with('error', 'File untuk revisi terakhir tidak ditemukan.');
        }

        $file = CollectFile::find($fileId);

        if (!$file) {
            return redirect()->back()->with('error', 'File tidak ditemukan di database.');
        }

        // 2. Buat URL publik ke file (pastikan `php artisan storage:link` sudah dijalankan)
        $fileUrl = asset('storage/' . ltrim($file->link, '/'));


        // 3. Kirim ke view
        return view('newreports.documentview.report', compact('fileUrl', 'result', 'last_rev'));
    }
    public function lastpdffilerev($rev_id)
    {
        // Temukan report berdasarkan ID dengan eager loading pada relasi 'newprogressreporthistory'
        $result = Newprogressreporthistory::with([
            'latestFile',
        ])->find($rev_id);

        if (!$result) {
            return redirect()->back()->with('error', 'Data tidak ditemukan.');
        }

        $fileId = null;
        if ($result && $result->latestFile) {
            $fileId = $result->latestFile->id;
            $last_rev = $result->rev;
        }

        if (!$fileId) {
            return redirect()->back()->with('error', 'File untuk revisi terakhir tidak ditemukan.');
        }

        $file = CollectFile::find($fileId);

        if (!$file) {
            return redirect()->back()->with('error', 'File tidak ditemukan di database.');
        }

        // 2. Buat URL publik ke file (pastikan `php artisan storage:link` sudah dijalankan)
        $fileUrl = asset('storage/' . ltrim($file->link, '/'));


        // 3. Kirim ke view
        return view('newreports.documentview.report', compact('fileUrl', 'result', 'last_rev'));
    }
}
