<?php

namespace App\Http\Controllers;

use App\Models\HumanHour;
use Illuminate\Http\Request;
use Carbon\Carbon; // Import Carbon class
use Maatwebsite\Excel\Facades\Excel;
use Exception;
use App\Imports\RawprogressreportsImport;

use Illuminate\Support\Facades\Cache;

class HumanHourController extends Controller
{

    public function getChartData(Request $request)
    {
        $year = $request->query('year', date('Y'));

        // Buat daftar semua bulan dalam format 'Y-m'
        $months = [];
        for ($i = 1; $i <= 12; $i++) {
            $months[] = Carbon::create($year, $i, 1)->format('Y-m');
        }

        // Inisialisasi array untuk menyimpan total workload per bulan
        $monthlyWorkload = array_fill_keys($months, 0);

        // Ambil data dari tabel HumanHour berdasarkan tahun yang dipilih
        $chartDatas = HumanHour::where('year', $year)->get();

        // Looping data untuk mengisi workload dari database
        foreach ($chartDatas as $history) {
            $dateKey = Carbon::create($history->year, $history->month, 1)->format('Y-m');
            if (isset($monthlyWorkload[$dateKey])) {
                $monthlyWorkload[$dateKey] += $history->humanhours;
            }
        }

        // Format data untuk JSON response
        $responseData = [];
        foreach ($monthlyWorkload as $date => $workload) {
            $responseData[] = [
                'date' => $date,
                'workload' => $workload, // Akan tetap 0 jika tidak ada data
            ];
        }

        return response()->json($responseData);
    }


}
