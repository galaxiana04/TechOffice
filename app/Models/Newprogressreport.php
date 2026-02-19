<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Newprogressreport extends Model
{
    protected $table = 'newprogressreports'; // Sesuaikan dengan nama tabel di database
    protected $fillable = [
        'drafter_id',
        'temporystatus',
        'parent_revision_id',
        'dcr',
        'startreleasedate',
        'deadlinereleasedate',
        'documentkind_id',
        'level_id',
        'papersize',
        'sheet',
        'releasedagain',
        'status'
    ]; // Specify the fields that are mass assignable


    public function subtackMembers()
    {
        return $this->belongsToMany(SubTackMember::class, 'newprogressreport_subtack_member', 'newprogressreport_id', 'subtack_member_id')
            ->withTimestamps();
    }

    public function newbomkomats()
    {
        return $this->belongsToMany(Newbomkomat::class, 'newbomkomat_newprogressreport', 'newprogressreport_id', 'newbomkomat_id')
            ->withTimestamps();
    }


    public function histories()
    {
        return $this->hasMany(Newprogressreporthistory::class, 'newprogressreport_id');
    }
    public function newprogressreporthistory()
    {
        return $this->hasMany(Newprogressreporthistory::class, 'newprogressreport_id');
    }
    public function latestHistory()
    {
        return $this->hasOne(Newprogressreporthistory::class, 'newprogressreport_id')
            ->orderByRaw("CASE 
            WHEN rev = '0' THEN 0 
            ELSE ASCII(UPPER(rev)) - ASCII('A') + 1 
            END DESC");
    }

    public function getLatestRevAttribute()
    {
        $histories = collect($this->histories);

        // Sorting berdasarkan 'rev' secara descending
        $sortedHistories = $histories->sortByDesc('rev');

        // Ambil elemen pertama setelah sorting, jika ada
        $firstHistory = $sortedHistories->first();

        return $firstHistory ? $firstHistory : null;
    }


    // Relasi ke dirinya sendiri untuk parent
    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_revision_id');
    }



    // Relasi ke dirinya sendiri untuk children
    public function children()
    {
        return $this->hasMany(self::class, 'parent_revision_id');
    }

    public function newreport()
    {
        return $this->belongsTo(Newreport::class);
    }

    public function documentKind()
    {
        return $this->belongsTo(NewProgressReportDocumentKind::class, 'documentkind_id');
    }



    public function revisions()
    {
        return $this->hasMany(Revision::class);
    }

    public function levelKind()
    {
        return $this->belongsTo(NewProgressReportsLevel::class, 'level_id');
    }

    public function jobticketHistories()
    {
        return $this->hasMany(JobticketHistory::class, 'newprogressreport_id');
    }

    // Start a new task
    public function starttugasbaru()
    {
        $datawaktu = json_decode($this->temporystatus, true) ?? [];
        $datawaktu['start_time_awal'] = Carbon::now();
        $datawaktu['start_time'] = Carbon::now();
        $datawaktu['pause_time'] = null;
        $datawaktu['total_elapsed_seconds'] = 0;
        $datawaktu['statusrevisi'] = 'ditutup';
        $this->temporystatus = json_encode($datawaktu);
        $this->save();
    }

    // Pause the current task
    public function pausetugasbaru()
    {
        $datawaktu = json_decode($this->temporystatus, true) ?? [];
        if (isset($datawaktu['start_time'])) {
            $now = Carbon::now();
            $elapsed = $now->diffInSeconds($datawaktu['start_time']);
            $datawaktu['pause_time'] = $now;
            $datawaktu['total_elapsed_seconds'] += $elapsed;
        }
        $this->temporystatus = json_encode($datawaktu);
        $this->save();

        return true;
    }


    public function resumetugasbaru()
    {
        $datawaktu = json_decode($this->temporystatus, true) ?? [];
        if (isset($datawaktu['pause_time'])) {
            $datawaktu['start_time'] = Carbon::now();
            $datawaktu['pause_time'] = null;
        }
        $this->temporystatus = json_encode($datawaktu);
        $this->save();
        $pauseTime = new Carbon($datawaktu['pause_time']);
        $currentElapsedSeconds = $datawaktu['total_elapsed_seconds'] + $pauseTime->diffInSeconds(Carbon::now());

        return [
            'startTime' => Carbon::now(),
            'success' => 'Data berhasil diperbarui',
            'elapsedSeconds' => $currentElapsedSeconds
        ];
    }

    // Reset the current task
    public function resettugasbaru()
    {
        $datawaktu = [];

        $this->drafter = null;
        $this->temporystatus = json_encode($datawaktu);
        $this->revisions()->delete();
        $this->save();

        return true;
    }

    // Finish the current task and create a new revision
    public function selesaitugasbaru()
    {
        $revisionCount = $this->revisions()->count();
        $nextRevisionNumber = $revisionCount;
        $revisionName = $this->convertToAlphabetic($nextRevisionNumber);

        $temporystatus = json_decode($this->temporystatus, true) ?? [];
        $startTime = $temporystatus['start_time_awal'];
        $totalElapsedSeconds = $temporystatus['total_elapsed_seconds'] ?? 0;
        if (isset($temporystatus['pause_time'])) {
            $pauseTime = new Carbon($temporystatus['pause_time']);
            $totalElapsedSeconds += $pauseTime->diffInSeconds(Carbon::now());
        }

        $newRevisionData = [
            'revisionname' => $revisionName,
            'end_time_run' => Carbon::now(),
            'revision_status' => "belum divalidasi",
            'total_elapsed_seconds' => $totalElapsedSeconds,
        ];

        $newRevision = $this->revisions()->create($newRevisionData);

        $temporystatus = [
            'start_time' => null,
            'pause_time' => null,
            'total_elapsed_seconds' => 0,
            'statusrevisi' => "ditutup",
            'revisionlast' => $revisionName,
        ];
        $this->temporystatus = json_encode($temporystatus);
        $this->save();

        return [
            'success' => 'Data berhasil diperbarui',
            'elapsedSeconds' => $temporystatus['total_elapsed_seconds'],
            'lastKey' => $revisionName,
        ];
    }



    // Helper method to convert a number to an alphabetic string (e.g., 1 -> A, 2 -> B, ...)
    protected function convertToAlphabetic($number)
    {
        if ($number == 0) {
            return '0';
        }

        $alphabet = range('A', 'Z');
        $result = '';

        while ($number > 0) {
            $remainder = ($number - 1) % 26;
            $result = $alphabet[$remainder] . $result;
            $number = (int) (($number - $remainder) / 26);
        }

        return $result;
    }



    public function izinkanrevisitugasbaru()
    {

        $datawaktu = json_decode($this->temporystatus, true) ?? [];
        $datawaktu['statusrevisi'] = "dibuka";
        $this->temporystatus = json_encode($datawaktu);
        $this->save();
        $pauseTime = new Carbon($datawaktu['pause_time']);
        $currentElapsedSeconds = $datawaktu['total_elapsed_seconds'] + $pauseTime->diffInSeconds(Carbon::now());
        return [
            'success' => 'Data berhasil diperbarui',
            'elapsedSeconds' => $currentElapsedSeconds
        ];
    }




    /**
     * Mengambil data proyek untuk ditampilkan dalam bentuk Gantt Chart.
     *
     * Fungsi ini mengambil data laporan kemajuan proyek berdasarkan tipe proyek dan tanggal akhir (opsional),
     * lalu mengorganisirnya ke dalam struktur hierarkis yang cocok untuk visualisasi Gantt Chart.
     * Data yang dihasilkan mencakup informasi teknologi, unit, level, jenis dokumen, dan dokumen individual,
     * lengkap dengan status rilis, perhitungan waktu, dan persentase penyelesaian.
     *
     * @param string $project Nama atau judul tipe proyek yang akan diambil datanya.
     * @param string|null $end_date Tanggal akhir dalam format yang dapat diparsing oleh Carbon (opsional).
     *                              Jika tidak diberikan, akan menggunakan tanggal hari ini.
     * @return array Data proyek dalam bentuk array hierarkis yang siap digunakan untuk Gantt Chart.
     *               Mengembalikan array kosong jika tipe proyek tidak ditemukan.
     *
     * Struktur data yang dihasilkan:
     * - 'id': Identifikasi unik untuk setiap entitas (teknologi, unit, level, dll.).
     * - 'name': Nama entitas (misalnya nama unit, level, atau dokumen).
     * - 'parent': ID entitas induk dalam hierarki.
     * - 'start_plan' dan 'end_plan': Tanggal rencana mulai dan selesai.
     * - 'start_real' dan 'end_real': Tanggal realisasi mulai dan selesai (berdasarkan data aktual).
     * - 'counts': Array yang berisi jumlah dokumen dalam berbagai status (Released, Unreleased, dll.).
     * - 'completed': Persentase penyelesaian berdasarkan rencana.
     * - 'completed_real': Persentase penyelesaian berdasarkan realisasi.
     * - 'color' dan 'color_real': Warna untuk visualisasi berdasarkan status rencana dan realisasi.
     * - 'sinkronstatus': Status sinkronisasi antara data rencana dan realisasi ('- Sinkron' atau '- Asinkron').
     *
     * Logika utama:
     * 1. Mengambil data master seperti nama dokumen dan level dari database.
     * 2. Menentukan tanggal 'hari ini' berdasarkan $end_date atau tanggal saat ini.
     * 3. Mengambil laporan proyek berdasarkan tipe proyek dan filter tanggal (jika ada).
     * 4. Memproses laporan untuk menghitung status rilis, waktu, dan hierarki (teknologi > unit > level > dokumen).
     * 5. Menghitung timeline dan persentase penyelesaian untuk setiap tingkat hierarki.
     * 6. Mengabaikan dokumen tertentu jika tipe proyek adalah 'KCI'.
     * 7. Mengembalikan data dalam struktur hierarkis yang siap digunakan.
     *
     * Catatan:
     * - Fungsi ini bergantung pada model seperti NewProgressReport, ProjectType, dan Carbon untuk parsing tanggal.
     * - Method seperti parseDate(), calculateCompletionPercentage(), dan setColor() diasumsikan ada di kelas ini.
     */


    public static function getProjectDataGantChart($project, $end_date)
    {

        $availableDocumentNames = NewProgressReportDocumentKind::pluck('name', 'id')->all();
        $levels = NewProgressReportsLevel::pluck('title', 'id')->all();
        $projectType = ProjectType::where('title', $project)->first();

        // Define "today" as the end_date if provided, otherwise use current date
        $today = !empty($end_date) ? Carbon::parse($end_date) : Carbon::today();
        if (!$projectType) {
            return [];
        }

        $projectData = [];
        $technology = [
            'id' => '1',
            'name' => 'TECHNOLOGY',
            'fontSymbol' => 'smile-o',
            'counts' => array_fill_keys([
                'Released',
                'Unreleased',
                'Ontimereleased',
                'Latereleased',
                'Ontimeunreleased',
                'Lateunreleased',
                'real_Released',
                'real_Unreleased'
            ], 0)
        ];

        $units = [];
        $levelDocs = [];
        $kindDocs = [];
        $uniqueDocs = [];



        // Query dasar
        $query = NewProgressReport::select([
            'id',
            'newreport_id',
            'documentkind_id',
            'level_id',
            'startreleasedate',
            'deadlinereleasedate',
            'status',
            'realisasidate',
            'nodokumen',
            'namadokumen',
            'created_at'
        ])->with([
            'newreport',
            'histories' => function ($q) {
                $q->where('rev', '0')->orderBy('created_at', 'asc'); // Ambil yang pertama berdasarkan created_at
            }
        ])->whereIn('newreport_id', $projectType->newreports()->pluck('id'));

        // Tambahkan pembatasan created_at jika end_date ada
        if (!empty($end_date)) {
            $query->where('created_at', '<=', Carbon::parse($end_date));
        }

        // Khusus untuk proyek KCI, filter unit tertentu
        if ($project === 'KCI') {
            $query->whereHas('newreport', fn($q) => $q->whereNotIn('unit', [
                'Shop Drawing',
                'Desain Carbody',
                'Welding Technology',
                'Preparation & Support',
                'MTPR'
            ]));
        } else {
            $query->whereHas('newreport', fn($q) => $q->whereNotIn('unit', [
                'MTPR'
            ]));
        }

        if (!empty($end_date)) {
            $reports = $query->get()->filter(function ($report) use ($end_date) {
                $realisasiDate = $report->histories->firstWhere('realisasidate', '!=', null)?->realisasidate ?? $report->realisasidate;
                return $realisasiDate ? Carbon::parse($realisasiDate)->lte(Carbon::parse($end_date)) : true;
            })->values();
        } else {
            $reports = $query->get();
        }

        $validReports = [];
        foreach ($reports as $report) {
            if (
                $report->documentkind_id && $report->level_id &&
                $report->startreleasedate && $report->deadlinereleasedate
            ) {
                $validReports[$report->id] = $report;
            }
        }

        // Hitung real counts dari semua laporan (termasuk yang tidak masuk level)
        foreach ($reports as $report) {
            $unitKey = "unit{$report->newreport_id}";
            if (!isset($units[$unitKey])) {
                $units[$unitKey] = [
                    'id' => $unitKey,
                    'name' => (string) $report->newreport->unit,
                    'parent' => '1',
                    'collapsed' => true,
                    'counts' => [
                        'Released' => 0,
                        'Unreleased' => 0,
                        'Ontimereleased' => 0,
                        'Latereleased' => 0,
                        'Ontimeunreleased' => 0,
                        'Lateunreleased' => 0,
                        'real_Released' => 0,
                        'real_Unreleased' => 0
                    ],
                    'levelKeys' => []
                ];
            }

            $isReleased = $report->status === 'RELEASED';
            if ($isReleased) {
                $technology['counts']['real_Released']++;
                $units[$unitKey]['counts']['real_Released']++;
            } else {
                $technology['counts']['real_Unreleased']++;
                $units[$unitKey]['counts']['real_Unreleased']++;
            }
        }

        // Proses laporan valid untuk hierarki dan timeline
        foreach ($validReports as $report) {
            $unitKey = "unit{$report->newreport_id}";
            $levelKey = "{$report->level_id}-{$report->newreport_id}";
            $kindKey = "{$report->documentkind_id}-{$report->newreport_id}-{$report->level_id}";
            $docKey = "{$report->documentkind_id}-{$report->newreport_id}-{$report->level_id}";

            $startTs = strtotime($report->startreleasedate);
            $endTs = strtotime($report->deadlinereleasedate);

            if (!isset($uniqueDocs[$docKey])) {
                $uniqueDocs[$docKey] = [
                    'unit' => (string) $report->newreport->unit,
                    'documentkind_id' => $report->documentkind_id,
                    'newreport_id' => $report->newreport_id,
                    'level_id' => $report->level_id,
                    'start_plan' => date('Y-m-d H:i:s', $startTs),
                    'end_plan' => date('Y-m-d H:i:s', $endTs),
                ];
            } else {
                $uniqueDocs[$docKey]['start_plan'] = min(strtotime($uniqueDocs[$docKey]['start_plan']), $startTs)
                    ? date('Y-m-d H:i:s', min(strtotime($uniqueDocs[$docKey]['start_plan']), $startTs))
                    : $uniqueDocs[$docKey]['start_plan'];
                $uniqueDocs[$docKey]['end_plan'] = max(strtotime($uniqueDocs[$docKey]['end_plan']), $endTs)
                    ? date('Y-m-d H:i:s', max(strtotime($uniqueDocs[$docKey]['end_plan']), $endTs))
                    : $uniqueDocs[$docKey]['end_plan'];
            }

            $technology['start_plan'] = min(strtotime($technology['start_plan'] ?? $report->startreleasedate), $startTs)
                ? date('Y-m-d H:i:s', min(strtotime($technology['start_plan'] ?? $report->startreleasedate), $startTs))
                : ($technology['start_plan'] ?? date('Y-m-d H:i:s', $startTs));
            $technology['end_plan'] = max(strtotime($technology['end_plan'] ?? $report->deadlinereleasedate), $endTs)
                ? date('Y-m-d H:i:s', max(strtotime($technology['end_plan'] ?? $report->deadlinereleasedate), $endTs))
                : ($technology['end_plan'] ?? date('Y-m-d H:i:s', $endTs));

            $units[$unitKey]['levelKeys'][] = $levelKey;

            $isReleased = $report->status === 'RELEASED';
            $deadlineDate = Carbon::parse($report->deadlinereleasedate);
            $realisasiDate = Carbon::parse($report->histories->firstWhere('realisasidate', '!=', null)?->realisasidate ?? $report->realisasidate);


            if ($isReleased) {
                $technology['counts']['Released']++;
                $units[$unitKey]['counts']['Released']++;
                $isOnTime = $realisasiDate->lte($deadlineDate);
                $technology['counts'][$isOnTime ? 'Ontimereleased' : 'Latereleased']++;
                $units[$unitKey]['counts'][$isOnTime ? 'Ontimereleased' : 'Latereleased']++;
            } else {
                $technology['counts']['Unreleased']++;
                $units[$unitKey]['counts']['Unreleased']++;
                $isOnTime = $today->lte($deadlineDate);
                $technology['counts'][$isOnTime ? 'Ontimeunreleased' : 'Lateunreleased']++;
                $units[$unitKey]['counts'][$isOnTime ? 'Ontimeunreleased' : 'Lateunreleased']++;
            }

            // HANYA MASUKAN DOKUMEN2 YANG BUKAN KCI
            if ($project !== 'KCI') {
                $projectData[] = [
                    'id' => (string) $report->id,
                    'name' => (string) "{$report->nodokumen}-{$report->namadokumen}",
                    'parent' => $kindKey,
                    'collapsed' => true,
                    'start_plan' => self::parseDate($report->startreleasedate),
                    'end_plan' => self::parseDate($report->deadlinereleasedate),
                    'completed' => self::calculateCompletionPercentage($isReleased ? 1 : 0, 0),
                    'color' => self::setColor($isReleased ? 1 : 0, 0, 'plan')
                ];
            }
        }

        foreach ($uniqueDocs as $key => $doc) {
            $levelKey = "{$doc['level_id']}-{$doc['newreport_id']}";
            if (!isset($levelDocs[$levelKey])) {
                $levelDocs[$levelKey] = [
                    'id' => $levelKey,
                    'name' => (string) ($levels[$doc['level_id']] ?? ''),
                    'parent' => "unit{$doc['newreport_id']}",
                    'collapsed' => true,
                    'start_plan' => $doc['start_plan'],
                    'end_plan' => $doc['end_plan'],
                    'counts' => $units["unit{$doc['newreport_id']}"]['counts']
                ];
            }

            $kindDocs[$key] = [
                'id' => $key,
                'name' => (string) ($availableDocumentNames[$doc['documentkind_id']] . " (" . $levels[$doc['level_id']] . ")"),
                'parent' => $levelKey,
                'collapsed' => true,
                'start_plan' => self::parseDate($doc['start_plan']),
                'end_plan' => self::parseDate($doc['end_plan']),
                'counts' => $units["unit{$doc['newreport_id']}"]['counts']
            ];
        }

        // Hitung ulang timeline unit berdasarkan level turunan
        foreach ($units as $unitKey => &$unit) {
            $maxEndTs = null;
            $minStartTs = null;
            foreach ($unit['levelKeys'] as $levelKey) {
                if (isset($levelDocs[$levelKey])) {
                    $levelStartTs = strtotime($levelDocs[$levelKey]['start_plan']);
                    $levelEndTs = strtotime($levelDocs[$levelKey]['end_plan']);
                    $minStartTs = $minStartTs === null ? $levelStartTs : min($minStartTs, $levelStartTs);
                    $maxEndTs = $maxEndTs === null ? $levelEndTs : max($maxEndTs, $levelEndTs);
                }
            }
            $unit['start_plan'] = $minStartTs ? date('Y-m-d H:i:s', $minStartTs) : null;
            $unit['end_plan'] = $maxEndTs ? date('Y-m-d H:i:s', $maxEndTs) : null;

            $unit['start_plan'] = self::parseDate($unit['start_plan'] ?? $technology['start_plan']);
            $unit['end_plan'] = self::parseDate($unit['end_plan'] ?? $technology['end_plan']);
            $unit['start_real'] = $unit['start_plan'];
            $unit['end_real'] = [$today->year, $today->month - 1, $today->day]; // Use $today instead of now()
            $unit['completed'] = ['amount' => 1];
            $unit['completed_real'] = self::calculateCompletionPercentage($unit['counts']['real_Released'], $unit['counts']['real_Unreleased']);
            $unit['color'] = self::setColor($unit['counts']['Released'], $unit['counts']['Unreleased'], 'plan');
            $unit['color_real'] = self::setColor($unit['counts']['Released'], $unit['counts']['Unreleased'], 'real');
            if (auth()->user()->rule == "MTPR") { //$project !== 'KCI' && 
                $unit['sinkronstatus'] = (($unit['counts']['real_Released'] + $unit['counts']['real_Unreleased']) ===
                    ($unit['counts']['Released'] + $unit['counts']['Unreleased']))
                    ? '- Sinkron' : '- Asinkron';
            }

            // Filter unit berdasarkan proyek
            $projectData[] = $unit;
            // if ($project !== 'KCI') {

            // } 
            // else {
            //     if (!in_array($unit['name'], ["Shop Drawing", "Preparation & Support"])) {
            //         $projectData[] = $unit;
            //     }
            // }
        }
        unset($unit);

        $technology['start_plan'] = self::parseDate($technology['start_plan']);
        $technology['end_plan'] = self::parseDate($technology['end_plan']);
        $technology['completed'] = ['amount' => 1];
        $technology['completed_real'] = self::calculateCompletionPercentage($technology['counts']['real_Released'], $technology['counts']['real_Unreleased']);
        $technology['color'] = self::setColor($technology['counts']['Released'], $technology['counts']['Unreleased'], 'plan');
        $technology['start_real'] = $technology['start_plan'];
        $technology['end_real'] = [$today->year, $today->month - 1, $today->day]; // Use $today instead of now()
        $technology['color_real'] = self::setColor($technology['counts']['Released'], $technology['counts']['Unreleased'], 'real');
        if (auth()->user()->rule == "MTPR") { //$project !== 'KCI' && 
            $technology['sinkronstatus'] = (($technology['counts']['real_Released'] + $technology['counts']['real_Unreleased']) ===
                ($technology['counts']['Released'] + $technology['counts']['Unreleased']))
                ? '- Sinkron' : '- Asinkron';
        }

        $projectData[] = $technology;

        foreach ($levelDocs as &$level) {
            $level['start_plan'] = self::parseDate($level['start_plan']);
            $level['end_plan'] = self::parseDate($level['end_plan']);
            $level['completed'] = ['amount' => 1];
            $level['completed_real'] = self::calculateCompletionPercentage($level['counts']['real_Released'], $level['counts']['real_Unreleased']);
            $level['color'] = self::setColor($level['counts']['Released'], $level['counts']['Unreleased'], 'plan');
            $level['start_real'] = $level['start_plan'];
            $level['end_real'] = $level['completed_real']['amount'] != 1 ? [$today->year, $today->month - 1, $today->day] : $level['end_plan']; // Use $today instead of now()
            $level['color_real'] = self::setColor($level['counts']['Released'], $level['counts']['Unreleased'], 'real');
            // HANYA MASUKAN LEVEL YANG BUKAN KCI
            if ($project !== 'KCI')
                $projectData[] = $level;
        }
        unset($level);

        // HANYA MASUKAN JENIS DOKUMEN YANG BUKAN KCI
        if ($project !== 'KCI') {
            foreach ($kindDocs as &$kind) {
                $kind['completed'] = self::calculateCompletionPercentage($kind['counts']['Released'], $kind['counts']['Unreleased']);
                $kind['completed_real'] = $technology['completed_real'];
                $kind['color'] = self::setColor($kind['counts']['Released'], $kind['counts']['Unreleased'], 'plan');
                $projectData[] = $kind;
            }
            unset($kind);
        }

        return $projectData;
    }





    public static function getHoursProjectData($year)
    {
        // Ambil data NewProgressReportHistory dengan filter tahun dan validasi
        $newProgressReportHistories = Newprogressreporthistory::with('newProgressReport.newreport.projectType')->whereYear('realisasidate', $year)->get()
            ->filter(function ($item) {
                return !is_null($item->realisasidate) &&
                    !is_null($item->rev);
            });



        $monthlyWorkload = [];

        // Loop utama untuk menghitung workload
        foreach ($newProgressReportHistories as $history) {
            $date = Carbon::parse($history->realisasidate)->format('Y-m');
            $projectName = $history->newProgressReport->newreport->projectType->title ?? 'Unknown';

            $releasedagain = $history->newProgressReport->releasedagain ?? 0;

            if (!isset($monthlyWorkload[$projectName][$date])) {
                $monthlyWorkload[$projectName][$date] = 0;
            }

            if (isset($history->papersize)) {
                $monthlyWorkload[$projectName][$date] += self::workloadcount($history->papersize, $history->sheet, $history->rev, $releasedagain);
            }
        }

        $projectData = [];
        foreach ($monthlyWorkload as $projectName => $dates) {
            foreach ($dates as $date => $workload) {
                $projectData[] = [
                    'project' => $projectName,
                    'date' => $date,
                    'workload' => $workload,
                ];
            }
        }

        return $projectData;
    }


    public static function getHoursProjectDatabyProject($projectTitle)
    {
        // Ambil data NewProgressReportHistory dengan relasi dan filter hanya yang valid
        // syarat rev, papersize, dan sheet
        $newProgressReportHistories = Newprogressreporthistory::with('newProgressReport.newreport.projectType')
            ->get()
            ->filter(function ($item) use ($projectTitle) {
                return !is_null($item->rev) && !is_null($item->papersize) && !is_null($item->sheet) &&
                    isset($item->newProgressReport->newreport->projectType->title) &&
                    $item->newProgressReport->newreport->projectType->title === $projectTitle;
            });

        $totalWorkload = 0;

        // Loop utama untuk menghitung workload
        foreach ($newProgressReportHistories as $history) {
            $releasedagain = $history->newProgressReport->releasedagain ?? 0;

            // Hitung workload jika properti papersize ada
            if (isset($history->papersize)) {
                $totalWorkload += self::workloadcount(
                    $history->papersize,
                    $history->sheet,
                    $history->rev,
                    $releasedagain
                );
            }
        }

        $totalworkload = number_format($totalWorkload, 2);



        $monthlyWorkload = [];

        // Loop utama untuk menghitung workload
        foreach ($newProgressReportHistories as $history) {
            $date = Carbon::parse($history->realisasidate)->format('Y-m');
            $projectName = $history->newProgressReport->newreport->projectType->title ?? 'Unknown';

            $releasedagain = $history->newProgressReport->releasedagain ?? 0;

            if (!isset($monthlyWorkload[$projectName][$date])) {
                $monthlyWorkload[$projectName][$date] = 0;
            }

            if (isset($history->papersize)) {
                $monthlyWorkload[$projectName][$date] += self::workloadcount($history->papersize, $history->sheet, $history->rev, $releasedagain);
            }
        }

        $projectData = [];
        foreach ($monthlyWorkload as $projectName => $dates) {
            foreach ($dates as $date => $workload) {
                $projectData[] = [
                    'project' => $projectName,
                    'date' => $date,
                    'workload' => $workload,
                ];
            }
        }

        $hasil = ['totalworkload' => $totalworkload, "montly-year" => $projectData];
        return $hasil;
    }

    public static function setColor($releasedCount, $unreleasedCount, $kind)
    {
        $total = $releasedCount + $unreleasedCount;

        if ($total == 0) {
            return '#ff0000';
        }

        $percentage = $releasedCount / $total;

        if ($kind == 'real') { // Hijau dan Merah (plan)
            return '#ff0000';
        } else {
            return [
                'pattern' => [
                    'path' => [
                        'd' => 'M 10 0 L 0 10',
                        'strokeWidth' => 2,
                        'stroke' => '#000'
                    ],
                    'width' => 10,
                    'height' => 10
                ]
            ];


            // return [
            //     'linearGradient' => ['x1' => 0, 'y1' => 0, 'x2' => 1, 'y2' => 0],
            //     'stops' => [
            //         [0, '#00f'],
            //         [1, '#00f']
            //     ]
            // ];
        }
    }
    public static function parseDate($date)
    {
        if (!$date) {
            return [now()->year, now()->month - 1, now()->day];
        }

        $parts = explode('-', $date);
        if (count($parts) !== 3) {
            return [now()->year, now()->month - 1, now()->day];
        }

        $year = (int) $parts[0];
        $month = (int) ltrim($parts[1], '0') - 1; // Bulan dimulai dari 0
        $day = (int) $parts[2];

        return $year > 2000 ? [$year, $month, $day] : [now()->year, now()->month - 1, now()->day];
    }

    public static function calculateCompletionPercentage($releasedCount, $unreleasedCount)
    {
        // Calculate the total count and avoid division by zero
        $total = $releasedCount + $unreleasedCount;

        // Handle the case where total is zero (gray color indicating no progress)
        if ($total == 0) {
            return [
                'amount' => 0,
                'fill' => '#0b0',
            ];
        }

        // Calculate the completion percentage
        $percentage = $releasedCount / $total;

        // Round the percentage to 2 decimal places
        $percentage = round($percentage, 2);

        // Handle milestone (all tasks released, full completion)
        if ($releasedCount > 0 && $unreleasedCount == 0) {
            return [
                'amount' => 1,

                'fill' => '#0b0',
            ];
        }

        // Return the rounded percentage
        return [
            'amount' => $percentage,

            'fill' => '#0b0',
        ];
    }

    public static function workloadcount($papersize, $sheet, $rev, $releasedagain)
    {

        #### Aturan
        #### Perhitungan workload ditentukan oleh:
        #### 1. Ukuran kertas gambar (A4, A3, A2, A1)
        #### 2. Jumlah lembar gambar (sheet)
        #### 3. Status revisi dokumen (rev)
        #### 4. Status rilis ulang dokumen (releasedagain)

        #### Faktor revisi (revequal):
        #### - Rev = 0 dan belum pernah dirilis ulang  → faktor = 1
        ####   (pekerjaan pembuatan awal, effort penuh)
        #### - Rev = 0 dan dirilis ulang              → faktor = 0.05
        ####   (perubahan sangat minor)
        #### - Rev > 0                                → faktor = 0.5
        ####   (revisi menengah)

        #### Bobot ukuran kertas:
        #### - A4 = 4
        #### - A3 = 8
        #### - A2 = 16
        #### - A1 = 32

        #### Rumus:
        #### Workload = Bobot Ukuran Kertas × Jumlah Sheet × Faktor Revisi
        $revequal = 1;
        if ($rev == "0" && $releasedagain == 0) {
            $revequal = 1;
        } elseif ($rev == "0" && $releasedagain == 1) {
            $revequal = 0.05;
        } else {
            $revequal = 0.5;
        }

        if ($papersize == 'A4') {
            return 4 * $sheet * $revequal;
        }
        if ($papersize == 'A3') {
            return 8 * $sheet * $revequal;
        }
        if ($papersize == 'A2') {
            return 16 * $sheet * $revequal;
        }
        if ($papersize == 'A1') {
            return 32 * $sheet * $revequal;
        }
    }
}
