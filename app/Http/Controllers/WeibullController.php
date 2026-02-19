<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Models\ComponentIdentity;
use App\Models\ProjectType;
use App\Models\FailureRecord;
use App\Models\ProjectOperationProfile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use DateTime;
use Carbon\Carbon;

class WeibullController extends Controller
{

    public function index(Request $request)
    {
        $projectoperaionprofileall = ProjectOperationProfile::with('projectType')->get();

        $query = ComponentIdentity::with('operationProfile.projectType')
            ->withCount('failureRecords');

        // 🔽 FILTER BY PROJECT
        if ($request->filled('project_id')) {
            $query->whereHas('operationProfile', function ($q) use ($request) {
                $q->where('id', $request->project_id);
            });
        }

        $components = $query
            ->orderByDesc('failure_records_count')
            ->paginate(15)
            ->withQueryString(); // supaya pagination tidak reset filter

        // =============================
        // HITUNG WEIBULL
        // =============================
        foreach ($components as $component) {
            if ($component->failure_records_count >= 2) {
                $ttf = $component->failureRecords()
                    ->pluck('ttf_hours')
                    ->sort()
                    ->values()
                    ->toArray();

                [$beta, $eta] = ComponentIdentity::calculateBetaEta($ttf);

                $b10 = $eta * pow(-log(0.9), 1 / $beta);
                $b25 = $eta * pow(-log(0.75), 1 / $beta);
                $meanlife = array_sum($ttf) / count($ttf);

                $failurePhase = ComponentIdentity::determineFailurePhase($beta)->phase;
                $gammaMTTF = $eta * ComponentIdentity::gammaApprox(1 + (1 / $beta));
                $component->weibull = (object) [
                    'beta' => round($beta, 4),
                    'eta' => round($eta, 2),
                    'b10' => round($b10),
                    'b25' => round($b25),
                    'meanlife_actual' => round($meanlife),

                    'meanlife_gammafunction' => is_finite($gammaMTTF)
                        ? round($gammaMTTF)
                        : null,
                    'meanlifetype' => $component->is_repairable ? 'MTBF' : 'MTTF',
                    'failure_count' => count($ttf),
                    'analysis_date' => today()->format('d/m/Y'),
                    'failure_phase' => $failurePhase,
                ];
            } else {
                $component->weibull = null;
            }
        }

        return view('weibull.dashboard', compact(
            'components',
            'projectoperaionprofileall'
        ));
    }

    public function show(ComponentIdentity $component)
    {
        $component->load(['failureRecords' => fn($q) => $q->orderBy('failure_date')]);

        $meanlifetype = $component->is_repairable ? 'MTBF' : 'MTTF';
        $chartData    = null;
        $latest       = null;
        $failurePhase = null;

        if ($component->failureRecords->count() >= 2) {
            $ttf = $component->failureRecords->pluck('ttf_hours')->sort()->values()->toArray();
            [$beta, $eta] = ComponentIdentity::calculateBetaEta($ttf);

            $b10      = $eta * pow(-log(0.9), 1 / $beta);
            $b25      = $eta * pow(-log(0.75), 1 / $beta);
            $meanlife = array_sum($ttf) / count($ttf);

            $maxT = max($ttf) * 1.2;
            $step = $maxT / 500;
            $t    = collect(range($step, $maxT, $step))->values()->toArray();

            $cdf = array_map(fn($time) => 1 - exp(-pow($time / $eta, $beta)), $t);

            $hazard = array_map(function ($time) use ($beta, $eta) {
                if ($time <= 0) return null;

                $value = ($beta / $eta) * pow($time / $eta, $beta - 1);

                if (is_nan($value) || is_infinite($value)) {
                    return null;
                }

                return $value;
            }, $t);

            $empirical = array_map(
                fn($i, $time) => ['x' => $time, 'y' => ($i + 1) / (count($ttf) + 1)],
                array_keys($ttf),
                $ttf
            );

            $chartData = compact('t', 'cdf', 'hazard', 'empirical', 'b10', 'b25');
            $gammaMTTF = $eta * ComponentIdentity::gammaApprox(1 + (1 / $beta));
            $latest = (object) [
                'beta'           => round($beta, 4),
                'eta'            => round($eta, 2),
                'b10'            => round($b10),
                'b25'            => round($b25),
                'meanlife_actual'       => round($meanlife),
                'meanlife_gammafunction' => is_finite($gammaMTTF)
                    ? round($gammaMTTF)
                    : null,
                'meanlifetype'   => $meanlifetype,
                'analysis_date'  => today(),
                'failure_count'  => count($ttf),
            ];

            // Logika phase sekarang dikeluarkan ke method terpisah
            $failurePhase = ComponentIdentity::determineFailurePhase($beta);
        }

        return view('weibull.detail', compact('component', 'latest', 'chartData', 'failurePhase'));
    }

    public function projectdashboard()
    {
        $allproject = ProjectType::all();
        $profiles = ProjectOperationProfile::with('projectType')->get();
        return view('weibull.project_dashboard', compact('profiles', 'allproject'));
    }

    public function storeProject(Request $request)
    {
        $request->validate([
            'project_type_id' => 'required|exists:project_types,id',
            'daily_operation_hours' => 'required|numeric|min:1|max:24',
            'weekly_operation_days' => 'required|integer|min:1|max:7',
        ]);

        ProjectOperationProfile::create($request->only('project_type_id', 'daily_operation_hours', 'weekly_operation_days'));

        return redirect()->route('weibull.project-dashboard')
            ->with('success', 'Project berhasil ditambahkan!');
    }

    public function updateProject(Request $request, ProjectOperationProfile $project)
    {
        $request->validate([
            'project_type_id' => 'required|exists:project_types,id',
            'daily_operation_hours' => 'required|numeric|min:1|max:24',
            'weekly_operation_days' => 'required|integer|min:1|max:7',
        ]);

        $project->update($request->only('project_type_id', 'daily_operation_hours', 'weekly_operation_days'));

        return redirect()->route('weibull.project-dashboard')
            ->with('success', 'Project berhasil diperbarui!');
    }

    public function deleteProject(ProjectOperationProfile $project)
    {
        $project->delete();
        return redirect()->route('weibull.project-dashboard')
            ->with('success', 'Project berhasil dihapus!');
    }

    public function createFailure()
    {
        $profiles = ProjectOperationProfile::with('projectType')->get();
        // Ambil data unik untuk dropdown
        $l1s = ComponentIdentity::distinct()->orderBy('component_l1')->pluck('component_l1')->filter()->unique();
        $l2s = ComponentIdentity::distinct()->orderBy('component_l2')->pluck('component_l2')->filter()->unique();
        $l3s = ComponentIdentity::distinct()->orderBy('component_l3')->pluck('component_l3')->filter()->unique();
        $l4s = ComponentIdentity::distinct()->orderBy('component_l4')->pluck('component_l4')->filter()->unique();

        return view('weibull.create_failure', compact('profiles', 'l1s', 'l2s', 'l3s', 'l4s'));
    }

    public function calculationmethod()
    {
        return view('weibull.calculation_method');
    }

    public function storeFailure(Request $request)
    {
        $request->validate([
            'project_operation_profile_id' => 'required|exists:project_operation_profiles,id',
            'component_l1' => 'required|string|max:255',
            'component_l2' => 'nullable|string|max:255',
            'component_l3' => 'nullable|string|max:255',
            'component_l4' => 'nullable|string|max:255',
            'is_repairable' => 'required|boolean',
            'start_date'   => 'required|date_format:d/m/Y',
            'failure_date' => 'required|date_format:d/m/Y',
            'failure_time' => 'required|date_format:H:i',
            'trainset' => 'nullable|string|max:255',
            'service_type' => 'nullable|string|max:255',
            'is_new' => 'nullable|boolean',
            'train_no' => 'nullable|string|max:255',
            'car_type' => 'nullable|string|max:255',
            'relation' => 'nullable|string|max:255',
            'problemdescription' => 'nullable|string|max:500',
            'solution' => 'nullable|string|max:500',
            'cause_classification' => 'nullable|string|max:255',
        ]);

        try {
            $profileId = $request->project_operation_profile_id;
            $start = DateTime::createFromFormat('d/m/Y', $request->start_date);
            $failDate = DateTime::createFromFormat('d/m/Y', $request->failure_date);
            $failTime = DateTime::createFromFormat('H:i', $request->failure_time);

            if (!$start || !$failDate || !$failTime) {
                throw new \Exception('Format tanggal atau waktu tidak valid. Gunakan dd/mm/yyyy dan HH:MM.');
            }

            $failDateTime = (clone $failDate)->setTime(
                $failTime->format('H'),
                $failTime->format('i'),
                0
            );

            $profile = ProjectOperationProfile::findOrFail($profileId);
            $daily_hours = $profile->daily_operation_hours;
            $weekly_days = $profile->weekly_operation_days; // misal 6 berarti 6 hari operasi per minggu
            $workDays = ComponentIdentity::countWorkingDays($start, $failDate, 7 - $weekly_days);
            // Hitung TTF jam
            $ttf_hours = $workDays * $daily_hours
                + $failTime->format('H')
                + ($failTime->format('i') / 60);

            if ($ttf_hours <= 0) {
                throw new \Exception('Tanggal dan waktu kegagalan harus setelah tanggal mulai operasi.');
            }

            $identity = ComponentIdentity::where([
                'project_operation_profile_id' => $profileId,
                'component_l1' => $request->component_l1,
                'component_l2' => $request->component_l2 ?: null,
                'component_l3'          => $request->component_l3 ?: null,
                'component_l4'          => $request->component_l4 ?: null,
            ])->first();

            if (!$identity) {
                // Komponen BARU → attach ke profile
                $identity = ComponentIdentity::create([
                    'project_operation_profile_id' => $profileId,
                    'component_l1' => $request->component_l1,
                    'component_l2' => $request->component_l2 ?: null,
                    'component_l3'          => $request->component_l3 ?: null,
                    'component_l4'          => $request->component_l4 ?: null,
                    'is_repairable' => (bool) $request->is_repairable,
                ]);
            } else {
                // Komponen SUDAH ADA → cek konsistensi
                if ($identity->is_repairable != (bool) $request->is_repairable) {
                    throw new \Exception(
                        'Tipe repairable / non-repairable tidak konsisten dengan data komponen yang sudah ada.'
                    );
                }
            }


            FailureRecord::updateOrCreate(
                [
                    'component_identity_id' => $identity->id,
                    'start_date'            => $start->format('Y-m-d'),
                    'failure_date'          => $failDate->format('Y-m-d'),
                    'failure_time'          => $failTime->format('H:i:00'),
                ],
                [
                    'ttf_hours'              => round($ttf_hours, 2),
                    'workdays'              => $workDays,
                    'source_file'            => 'Manual Input',
                    'service_type'           => $request->service_type,
                    'is_new'                 => $request->is_new,
                    'trainset'               => $request->trainset,
                    'train_no'               => $request->train_no,
                    'car_type'               => $request->car_type,
                    'relation'               => $request->relation,
                    'problemdescription'     => $request->problemdescription,
                    'solution'               => $request->solution,
                    'cause_classification'   => $request->cause_classification,
                ]
            );


            // Kembali ke form dengan pesan sukses (tetap di halaman form)
            return redirect()->route('weibull.create')
                ->with('success', 'Data kegagalan komponen berhasil disimpan! Analisis Weibull telah terupdate.')
                ->withInput(); // opsional, agar form kosong atau tetap terisi

        } catch (\Exception $e) {
            // Kembali ke form dengan pesan error
            return redirect()->route('weibull.create')
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    // ======================
    // Upload Excel dan Import
    // ======================
    public function showUploadFormExcel()
    {
        return view('weibull.uploadexcel');
    }

    public function importExcelsistem(Request $request)
    {
        $jenisupload = $request->jenisupload;

        if ($jenisupload == "formatprogress") {
            return $this->formatprogress($request);
        }

        return response()->json(['error' => 'Jenis upload tidak dikenal'], 400);
    }

    public function formatprogress(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls',
        ]);

        $file = $request->file('file');
        $importedCount = 0;
        $skippedCount = 0;

        // Cache project types untuk performa
        $projectTypes = Cache::remember('project_types_titles', 3600, function () {
            return ProjectType::pluck('id', 'title')->toArray();
        });

        DB::beginTransaction();
        try {
            $spreadsheet = IOFactory::load($file->getRealPath());
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray(null, true, true, true);

            // Hapus header row
            array_shift($rows);

            // Proses dan validasi data
            $processedData = $this->progressreportexported($rows);

            foreach ($processedData as $item) {
                // Skip row jika data esensial kosong atau tanggal tidak valid
                if (
                    empty($item['project']) ||
                    empty($item['l1']) ||
                    $item['start_date'] === null ||
                    $item['failure_date'] === null ||
                    empty($item['failure_time'])
                ) {
                    Log::warning('Skipping invalid row during import', ['data' => $item]);
                    $skippedCount++;
                    continue;
                }

                // Skip jika data esensial kosong
                if (
                    empty($item['project']) || empty($item['l1']) ||
                    $item['start_date'] === null
                ) {
                    Log::warning('Skipping invalid row during import', ['data' => $item]);
                    continue;
                }

                $projectTitle = trim($item['project']);
                $projectType = ProjectType::firstOrCreate(['title' => $projectTitle]);

                // Default profile jika belum ada
                $profile = ProjectOperationProfile::firstOrCreate(
                    ['project_type_id' => $projectType->id],
                    [
                        'daily_operation_hours' => 12,
                        'weekly_operation_days' => 6,
                    ]
                );

                // Cari atau buat ComponentIdentity
                $identity = ComponentIdentity::where([
                    'project_operation_profile_id' => $profile->id,
                    'component_l1' => $item['l1'],
                    'component_l2' => $item['l2'],
                    'component_l3' => $item['l3'],
                    'component_l4' => $item['l4'],
                ])->first();

                if (!$identity) {
                    $identity = ComponentIdentity::create([
                        'project_operation_profile_id' => $profile->id,
                        'component_l1' => $item['l1'],
                        'component_l2' => $item['l2'],
                        'component_l3' => $item['l3'],
                        'component_l4' => $item['l4'],
                        'is_repairable' => $item['is_repairable'],
                    ]);
                } else {
                    // Cek konsistensi is_repairable
                    if ($identity->is_repairable != $item['is_repairable']) {
                        Log::warning('Inconsistent repairable flag, skipping row', ['data' => $item, 'existing' => $identity->is_repairable]);
                        $skippedCount++;
                        continue;
                    }
                }

                // Parse datetime (tetap sama)
                $startDate = DateTime::createFromFormat('d/m/Y', $item['start_date']);
                $failureDate = DateTime::createFromFormat('d/m/Y', $item['failure_date']);
                $failureTime = DateTime::createFromFormat('H:i', $item['failure_time']);

                if (!$startDate || !$failureDate || !$failureTime) {
                    Log::warning('Invalid date/time format', ['data' => $item]);
                    $skippedCount++;
                    continue;
                }

                // ================ LOGIKA FINAL: PASTI 480 JAM ================
                $workDays = ComponentIdentity::countWorkingDays($startDate, $failureDate, 7 - $profile->weekly_operation_days);

                // Match Excel 100%: Hari Kerja × daily_hours + prorata jam kegagalan
                $ttf_hours = $workDays * $profile->daily_operation_hours
                    + (int)$failureTime->format('H')
                    + ((int)$failureTime->format('i') / 60.0);

                $ttf_hours = round($ttf_hours, 2);

                // Simpan FailureRecord (tetap sama)
                FailureRecord::updateOrCreate(
                    [
                        'component_identity_id' => $identity->id,
                        'start_date'            => $startDate->format('Y-m-d'),
                        'failure_date'          => $failureDate->format('Y-m-d'),
                        'failure_time'          => $failureTime->format('H:i:00'),
                    ],
                    [
                        'ttf_hours'             => round($ttf_hours, 2),
                        'workdays'              => $workDays,
                        'source_file'           => 'Excel Import - Progress Format',
                        'trainset'              => $item['trainset'],
                        'train_no'              => $item['train_no'],
                        'service_type'          => $item['service_type'],
                        'is_new'                => $item['is_new'],
                        'car_type'              => $item['car_type'],
                        'relation'              => $item['relation'],
                        'problemdescription'    => $item['problemdescription'],
                        'solution'              => $item['solution'],
                        'cause_classification'  => $item['cause_classification'],
                    ]
                );

                $importedCount++;
            }

            DB::commit();

            return response()->json([
                'message' => "Berhasil mengimpor {$importedCount} data kegagalan komponen. "
                    . ($skippedCount > 0 ? "{$skippedCount} baris dilewati karena invalid." : ''),
                'imported_count' => $importedCount,
                'skipped_count'  => $skippedCount,
                // 'processed_data' => $processedData, // uncomment jika ingin preview semua data yang diproses
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Weibull import formatprogress failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'error' => 'Gagal mengimpor data: ' . $e->getMessage()
            ], 500);
        }
    }


    public function progressreportexported($importedData)
    {
        $validRows = [];

        // Helper function untuk parse tanggal Excel
        $parseExcelDate = function ($value) {
            if (empty($value)) return null;

            // Jika sudah DateTime
            if ($value instanceof \DateTimeInterface) {
                return Carbon::instance($value)->startOfDay();
            }

            // Jika angka (Excel serial date)
            if (is_numeric($value)) {
                return Carbon::create(1899, 12, 30)->addDays($value)->startOfDay();
            }

            // Jika string
            $value = trim($value);
            $formats = ['d-m-Y', 'd-M-y', 'd-M-Y', 'Y-m-d', 'd/m/Y', 'm/d/Y'];
            foreach ($formats as $fmt) {
                try {
                    $dt = Carbon::createFromFormat($fmt, $value);
                    if ($dt) return $dt->startOfDay();
                } catch (\Exception $e) {
                    // skip format gagal
                }
            }

            // Fallback: parse otomatis
            try {
                return Carbon::parse($value)->startOfDay();
            } catch (\Exception $e) {
                Log::warning('Gagal parse tanggal', ['value' => $value]);
                return null;
            }
        };

        foreach ($importedData as $index => $row) {
            if (empty(array_filter($row))) continue;

            $project     = trim($row['A'] ?? '');
            $l1          = trim($row['B'] ?? '');
            $l2          = trim($row['C'] ?? '');
            $l3          = trim($row['D'] ?? '');
            $l4          = trim($row['E'] ?? '');
            $start_date  = $row['F'] ?? null;
            $failure_date = $row['G'] ?? null;
            $failure_time = trim($row['H'] ?? '');
            $repairable  = trim($row['I'] ?? '');
            $service_type = trim($row['J'] ?? '');
            $isnew       = trim($row['K'] ?? '');
            $trainset    = trim($row['L'] ?? '');
            $train_no    = trim($row['M'] ?? '');
            $car_type    = trim($row['N'] ?? '');
            $relation    = trim($row['O'] ?? '');
            $problemdescription = trim($row['P'] ?? '');
            $solution    = trim($row['Q'] ?? '');
            $cause_classification = trim($row['R'] ?? '');

            // Log warning jika kolom wajib kosong tapi tetap proses
            if (empty($project) || empty($l1) || empty($failure_time)) {
                Log::warning('Missing required fields', ['row' => $index + 2, 'data' => $row]);
            }

            $is_repairable = in_array(strtolower($repairable), ['1', 'true', 'yes', 'repairable'], true) ? 1 : 0;

            // Parse tanggal
            $startdate = $parseExcelDate($start_date);
            $failuredate = $parseExcelDate($failure_date);


            $validRows[] = [
                'project' => $project,
                'l1' => $l1,
                'l2' => $l2 ?: null,
                'l3' => $l3 ?: null,
                'l4' => $l4 ?: null,
                'start_date' => $startdate ? $startdate->format('d/m/Y') : null,
                'failure_date' => $failuredate ? $failuredate->format('d/m/Y') : null,
                'failure_time' => $failure_time,
                'is_repairable' => $is_repairable,
                'service_type' => $service_type,
                'trainset' => $trainset,
                'is_new' => $isnew,
                'train_no' => $train_no,
                'car_type' => $car_type,
                'relation' => $relation,
                'problemdescription' => $problemdescription,
                'solution' => $solution,
                'cause_classification' => $cause_classification,
            ];
        }

        return $validRows;
    }

    // konsep ttf_new bisa dipertimbangkan untuk implementasi di masa depan
    // 1️⃣ ttf_new → untuk perhitungan B10/B25 dan β/η
    // Hanya mengambil record is_new = true, yaitu kegagalan pertama setelah komponen baru atau replacement.
    // Tidak mengikutsertakan TTF hasil repair.
    // Alasan: Distribusi Weibull B10/B25 merepresentasikan umur “as new”, jadi kalau pakai TTF repairable kumulatif, B10/B25 akan terlalu tinggi dan tidak realistis.

}
