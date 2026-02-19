<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DateTime;
use DateInterval;
use DatePeriod;

class ComponentIdentity extends Model
{
    use HasFactory;

    protected $fillable = ['project_operation_profile_id', 'component_l1', 'component_l2', 'component_l3', 'component_l4', 'is_repairable'];
    protected $casts = [
        'is_repairable' => 'boolean',
    ];

    public function operationProfile()
    {
        return $this->belongsTo(ProjectOperationProfile::class, 'project_operation_profile_id');
    }
    public function failureRecords()
    {
        return $this->hasMany(FailureRecord::class, 'component_identity_id');
    }

    /**
     * Menentukan fase kegagalan berdasarkan nilai shape parameter β (beta) Weibull.
     *
     * @param float $beta
     * @return object|null
     */
    public static function determineFailurePhase(float $beta): ?object
    {
        if ($beta < 0.9) {
            $phase = 'INFANT MORTALITY';
            $badge = $beta < 0.6 ? 'danger' : 'warning';

            $description = $beta < 0.6
                ? 'Kegagalan dini ekstrem. Hampir pasti cacat manufaktur, salah instalasi, atau salah spesifikasi.'
                : 'Kegagalan dini dominan. Fokus burn-in, inspeksi awal, dan Root Cause Analysis (RCA).';
        } elseif ($beta <= 1.1) {
            $phase       = 'RANDOM FAILURE';
            $badge       = 'primary';
            $description = 'Hazard relatif konstan. Kegagalan bersifat acak. Preventive maintenance berbasis waktu tidak efektif. Gunakan condition / predictive maintenance.';
        } else {
            $phase = 'WEAR-OUT';
            $badge = $beta <= 1.5 ? 'info' : 'danger';

            $description = $beta <= 1.5
                ? 'Keausan mulai terbentuk. Monitoring kondisi dan tren degradasi disarankan.'
                : 'Keausan dominan. Replacement berbasis umur (B10/B25) wajib diterapkan.';
        }

        return (object) [
            'phase'       => $phase,
            'badge'       => $badge,
            'description' => $description,
        ];
    }
    public static function calculateBetaEta(array $ttf): array
    {
        // Hitung jumlah data kegagalan (n)
        $n = count($ttf);

        // Buat ranking dari 1 sampai n (urutan kegagalan)
        $ranks = range(1, $n);

        // Hitung Median Rank menggunakan Benard's approximation: (i - 0.3)/(n + 0.4)
        // Ini adalah estimasi probabilitas kegagalan kumulatif yang lebih akurat untuk data kecil
        $median_ranks = array_map(fn($i) => ($i - 0.3) / ($n + 0.4), $ranks);

        // Transformasi natural log dari Time to Failure (ln(TTF))
        $ln_ttf = array_map('log', $ttf);

        // Transformasi untuk sumbu Y pada Weibull plot: ln(-ln(1 - F))
        // Di mana F adalah median rank (probabilitas kegagalan empiris)
        $ln_neg_ln = array_map(fn($mr) => log(-log(1 - $mr)), $median_ranks);

        // Hitung nilai-nilai statistik untuk regresi linier
        $sum_x   = array_sum($ln_ttf);                    // Σ ln(TTF)
        $sum_y   = array_sum($ln_neg_ln);                // Σ ln(-ln(1-F))
        $sum_xy  = array_sum(array_map(fn($x, $y) => $x * $y, $ln_ttf, $ln_neg_ln)); // Σ (ln(TTF) * ln(-ln(1-F)))
        $sum_x2  = array_sum(array_map(fn($x) => $x * $x, $ln_ttf));                  // Σ (ln(TTF))²

        // Hitung denominator untuk slope (beta)
        // Rumus: n * Σx² - (Σx)²
        $denom = $n * $sum_x2 - $sum_x * $sum_x;

        // Hindari pembagian dengan nol (meski sangat jarang terjadi)
        if (abs($denom) < 1e-10) {
            $denom = 1e-10;
        }

        // Hitung slope regresi = parameter β (shape parameter)
        // Rumus: [n * Σxy - Σx * Σy] / [n * Σx² - (Σx)²]
        $beta = ($n * $sum_xy - $sum_x * $sum_y) / $denom;

        // Safeguard: pastikan nilai beta masuk akal
        if ($beta <= 0.01 || is_nan($beta) || is_infinite($beta)) {
            $beta = 1.0; // Default ke distribusi eksponensial (random failure)
        }
        if ($beta > 100) {
            $beta = 100; // Batasi nilai ekstrem agar tidak overflow
        }

        // Hitung intercept dari regresi linier
        // Rumus: (Σy - β * Σx) / n
        $intercept = ($sum_y - $beta * $sum_x) / $n;

        // Hitung parameter η (scale / characteristic life)
        // Rumus: η = exp(-intercept / β)
        $eta = exp(-$intercept / $beta);

        // Safeguard untuk eta: jika hasil tidak valid, gunakan rata-rata TTF sebagai fallback
        if (is_nan($eta) || $eta <= 0 || is_infinite($eta)) {
            $eta = array_sum($ttf) / $n; // Fallback ke mean TTF
        }

        // Kembalikan parameter Weibull: [β, η]
        return [$beta, $eta];
    }

    /**
     * Gamma function wrapper (stabil & akurat)
     * Menggunakan lgamma() bawaan PHP
     */
    /**
     * Gamma Function menggunakan Lanczos Approximation
     * Akurat & stabil untuk kebutuhan Weibull MTTF
     */
    public static function gammaApprox(float $z): float
    {
        if ($z <= 0) {
            return NAN;
        }

        // Lanczos coefficients (g = 7, n = 9)
        static $p = [
            0.99999999999980993,
            676.5203681218851,
            -1259.1392167224028,
            771.32342877765313,
            -176.61502916214059,
            12.507343278686905,
            -0.13857109526572012,
            9.9843695780195716e-6,
            1.5056327351493116e-7
        ];

        // Reflection formula untuk z < 0.5
        if ($z < 0.5) {
            return M_PI / (sin(M_PI * $z) * self::gammaApprox(1 - $z));
        }

        $z -= 1;
        $x = $p[0];
        for ($i = 1; $i < count($p); $i++) {
            $x += $p[$i] / ($z + $i);
        }

        $t = $z + 7.5;

        return sqrt(2 * M_PI) * pow($t, $z + 0.5) * exp(-$t) * $x;
    }
    public static function countWorkingDays(DateTime $start, DateTime $end, int $weekly_holiday_count = 1)
    {
        // urutan PHP w: 0=Sun,1=Mon,...6=Sat
        $phpWeekDays = [1, 2, 3, 4, 5, 6, 0]; // Mon → Sun

        // ambil N hari libur terakhir
        $weekly_holidays = array_slice($phpWeekDays, -$weekly_holiday_count);

        $workDays = 0;
        $period = new DatePeriod(
            clone $start,
            new DateInterval('P1D'),
            (clone $end)->modify('+1 day') // inklusif
        );

        foreach ($period as $dt) {
            $weekday = (int)$dt->format('w'); // 0=Sun ... 6=Sat
            if (in_array($weekday, $weekly_holidays)) continue;
            $workDays++;
        }

        return $workDays;
    }
}
