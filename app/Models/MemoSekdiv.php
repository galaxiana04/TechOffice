<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MemoSekdiv extends Model
{
    use HasFactory;

    protected $table = 'memosekdivs';

    protected $fillable = [
        'documentname',
        'documentnumber',
        'project_type_id',
        'documentstatus',
        'documentkind'
    ];

    public function projectType()
    {
        return $this->belongsTo(ProjectType::class);
    }

    public function feedbacks()
    {
        return $this->hasMany(MemosekdivFeedback::class, 'memo_sekdiv_id');
    }

    public function smDecisions()
    {
        return $this->hasMany(MemoSekdivSmDecision::class);
    }
    public function timelines()
    {
        return $this->hasMany(MemoSekdivTimeline::class);
    }
    public function systemLogs()
    {
        return $this->morphMany(SystemLog::class, 'loggable');
    }
    public function notifsystem()
    {
        return $this->morphMany(Notification::class, 'notifmessage');
    }

    public function detailonedocument()
    {

        $sekdivfinalvalidation = "Nonaktif";
        $selfunitvalidation = "Nonaktif";
        $unitvalidation = "Nonaktif"; // Tambah variabel baru
        $temporaryrule = "GUEST";
        if (isset(auth()->user()->rule)) {
            $temporaryrule = auth()->user()->rule;
        }

        // Inisialisasi posisi
        [$this->posisi1, $this->posisi2, $this->posisi3, $this->posisi4] = [true, false, false, false];

        // Unit & SM
        $units = [
            'Product Engineering',
            'Mechanical Engineering System',
            'Electrical Engineering System',
            'Quality Engineering',
            'Desain Mekanik & Interior',
            'Desain Bogie & Wagon',
            'Desain Carbody',
            'Desain Elektrik',
            'Preparation & Support',
            'Welding Technology',
            'Shop Drawing',
            'Teknologi Proses',
            'RAMS',
            'MTPR'
        ];
        $smunits = ["Senior Manager Engineering", "Senior Manager Desain", "Senior Manager Teknologi Produksi", 'Manager MTPR'];

        // Singkatan unit
        $this->unitsingkatan = collect(array_merge($units, $smunits))
            ->filter(fn($unit) => !in_array($unit, ['MTPR', 'RAMS'])) // Pengecualian untuk MTPR dan RAMS
            ->mapWithKeys(fn($unit) => [$unit => self::singkatanUnit($unit)])
            ->toArray();


        // Validasi unit SM
        $unitpicvalidation = [];
        $projectpics = [];
        $smlist = [];
        $unitstepverificator = []; // Array untuk melacak status dan rank unit
        $currentOngoingUnit = null; // Melacak unit yang sedang "Ongoing"
        $rankCounter = 0; // Counter untuk rank urutan penyelesaian



        foreach ($this->smDecisions as $smdecision) {
            $unitUnderSms = $smdecision->unitUnderSms;
            $status = count($unitUnderSms) > 0 ? 'Aktif' : 'Ongoing';
            $smlist[$smdecision->smpositionname] = $status;

            foreach ($unitUnderSms as $unit) {
                $unitpicvalidation[$unit->unitname] = 'Ongoing';
                $projectpics[] = $unit->unitname;
            }

            // Jika ada unit di bawah SM, ubah posisi ke posisi2
            if ($status === 'Aktif') {
                [$this->posisi1, $this->posisi2] = [false, true];
            }
        }

        // Inisialisasi unitstepverificator untuk semua unit di projectpics
        foreach ($projectpics as $unit) {
            $unitstepverificator[$unit] = ["status" => "Access", "rank" => null];
        }

        // Jika semua SM statusnya "Aktif"
        if (count($smlist) > 0 && collect($smlist)->every(fn($status) => $status === 'Aktif')) {
            [$this->posisi1, $this->posisi2, $this->posisi3] = [false, false, true];
            $this->AllSMvalidation = "Aktif";
        }

        // Inisialisasi array untuk menyimpan log aktivitas (author dan time)
        $unitpicvalidation_array = [];

        $userinformations = $this->feedbacks;
        foreach ($userinformations as $userinformation) {
            $picname = $userinformation->pic;
            $levelname = $userinformation->level;
            if ($userinformation->condition2 == "feedback") {

                //selfunitvalidation awal
                if (
                    ($picname == $temporaryrule || $levelname == $temporaryrule) &&
                    in_array($userinformation->condition1, ["Approved", "Approved by Manager", "Terkirim"])
                ) {
                    $selfunitvalidation = "Aktif";
                }

                // Unit PIC Validation and Unit Validation
                if (!empty($projectpics)) {
                    if ($currentOngoingUnit === null && in_array($picname, $projectpics)) {
                        $unitstepverificator[$picname]["status"] = "Access";
                        $currentOngoingUnit = $picname;
                        foreach ($unitstepverificator as $key => $value) {
                            if (in_array($picname, $projectpics)) {
                                if ($key != $picname && $unitpicvalidation[$key] != "Aktif") {
                                    $unitstepverificator[$key]["status"] = "Not Access";
                                }
                            }
                        }
                    }
                    if (
                        in_array($picname, $projectpics) &&
                        in_array($userinformation->condition1, ["Approved", "Approved by Manager", "Terkirim"])
                    ) {
                        $unitpicvalidation[$picname] = "Aktif";
                        $unitstepverificator[$picname]["status"] = "Not Access";
                        $unitstepverificator[$picname]["rank"] = $rankCounter++;
                        // Simpan log aktivitas
                        $unitpicvalidation_array[$picname] = [
                            'author' => $userinformation->author,
                            'time' => $userinformation->created_at,
                        ];
                        $currentOngoingUnit = null; // Reset untuk membuka peluang unit berikutnya
                        // Set ulang unit lain ke "Access" jika belum "Aktif"
                        foreach ($unitstepverificator as $key => $value) {
                            if ($unitpicvalidation[$key] != "Aktif") {
                                $unitstepverificator[$key]["status"] = "Access";
                            }
                        }
                    }

                    // Check for "Ongoing" status
                    if (
                        in_array($picname, $projectpics) &&
                        ($userinformation->condition2 ?? '') == "feedback"
                    ) {
                        if ($unitpicvalidation[$picname] != "Aktif") {
                            $unitpicvalidation[$picname] = "Ongoing";
                        }
                    }
                }
            }
            if ($userinformation->level == "penutupdokumen") {
                [$this->posisi1, $this->posisi2, $this->posisi3, $this->posisi4] = [false, false, false, true];
            }
        }

        // Cek apakah semua unitpicvalidation = "Aktif"
        if (count($unitpicvalidation) > 0 && collect($unitpicvalidation)->every(fn($status) => $status === 'Aktif')) {
            $unitvalidation = "Aktif"; // Tambah variabel baru
            [$this->posisi1, $this->posisi2, $this->posisi3, $this->posisi4] = [false, false, false, true];
        }

        // Urutkan projectpics berdasarkan rank
        if (!empty($projectpics)) {
            usort($projectpics, function ($a, $b) use ($unitstepverificator) {
                $rankA = $unitstepverificator[$a]['rank'] ?? PHP_INT_MAX;
                $rankB = $unitstepverificator[$b]['rank'] ?? PHP_INT_MAX;
                return $rankA - $rankB;
            });
            $this->unitlaststep = end($projectpics); // Unit terakhir yang selesai
            $this->projectpics = $projectpics; // Simpan projectpics yang sudah diurutkan
        } else {
            $this->unitlaststep = null;
            $this->projectpics = [];
        }

        foreach ($userinformations as $userinformation) {
            $picname = $userinformation->pic;
            $levelname = $userinformation->level;

            if ($userinformation->level == "selesai") {
                [$this->posisi1, $this->posisi2, $this->posisi3, $this->posisi4] = [true, true, true, true];
                $sekdivfinalvalidation = "Aktif";
            }
        }
        $this->selfunitvalidation = $selfunitvalidation;
        $this->unitvalidation = $unitvalidation;
        $this->sekdivfinalvalidation = $sekdivfinalvalidation;
        $this->unitstepverificator = $unitstepverificator;
        $this->sekdivvalidation = "Aktif";
        $this->smunitpicvalidation = $smlist;
        $this->unitpicvalidation = $unitpicvalidation;



        return $this;
    }


    public static function singkatanUnit($namaUnit)
    {
        $singkatan = "";
        $kata = explode(" ", $namaUnit);
        foreach ($kata as $k) {
            $singkatan .= substr($k, 0, 1);
        }
        return $singkatan;
    }

    public function memoSekdivAccesses()
    {
        return $this->hasMany(MemoSekdivAccess::class);
    }
}
