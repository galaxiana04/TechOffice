<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use App\Models\User;

class WhatsAppService
{




    // PENSIUN
    public static function sendWaMessage($listnohp, $pesan)
    {
        // Initialize an array to store formatted phone numbers
        $formattedNumbers = [];

        foreach ($listnohp as $nohp) {

            if (strlen($nohp) < 15) {
                // Remove '+' character if present
                $nohp = ltrim($nohp, '+');

                // Replace '08' prefix with '628'
                if (substr($nohp, 0, 2) === '08') {
                    $nohp = '628' . substr($nohp, 2);
                } elseif (substr($nohp, 0, 3) !== '628') {
                    continue; // Skip invalid phone numbers
                }
            }


            // Add formatted number to the array
            $formattedNumbers[] = $nohp;
        }

        // Check if there are any valid phone numbers
        if (empty($formattedNumbers)) {
            return response()->json([
                'message' => 'Tidak ada nomor telepon yang valid'
            ], 400);
        }


        if (is_numeric($pesan)) {
            // Jika $pesan bisa diubah menjadi integer, kirim sebagai file
            $response = Http::post('https://diyloveheart.in/api/wamessages/post', [
                'phone_numbers' => $formattedNumbers,
                'message' => "" . $pesan, // Ubah ke integer
                'wamessagekind' => "file",
                'idtoken' => env('WA_IDTOKEN'),
                'accesstoken' => env('WA_ACCESSTOKEN'),
            ]);
        } else {
            // Jika $pesan tidak bisa diubah menjadi integer, kirim sebagai teks
            $response = Http::post('https://diyloveheart.in/api/wamessages/post', [
                'phone_numbers' => $formattedNumbers,
                'message' => $pesan, // Kirim sebagai teks
                'wamessagekind' => "text",
                'idtoken' => env('WA_IDTOKEN'),
                'accesstoken' => env('WA_ACCESSTOKEN'),
            ]);
        }


        // Check if the response is successful
        if ($response->successful()) {
            return response()->json([
                'message' => 'Data berhasil disimpan',
                'data' => $response->json()
            ], 201);
        } else {
            return response()->json([
                'message' => 'Gagal menyimpan data',
                'status' => $response->status(),
                'error' => $response->body()
            ], $response->status());
        }
    }

    public static function ujisendunit($unit, $message)
    {
        $unitNumbers = [
            "Quality Engineering" => '120363375608982413',
            "Electrical Engineering System" => '120363359225428796',
            "Mechanical Engineering System" => '120363376758705413',
            "Product Engineering" => '120363378278764767',
            "Desain Mekanik & Interior" => '120363375414522511',
            "Desain Carbody" => '120363354845494246',
            "RAMS" => '120363376827304062',
            "Desain Bogie & Wagon" => '120363358094687724',
            "Desain Elektrik" => '120363357893948659',
            "Preparation & Support" => '120363377589869848',
            "Welding Technology" => '120363394496310257',
            "Teknologi Proses" => '120363395166185110',
            "QC INC" => '120363378030037045',
            "PPO" => '120363380589312298',
            "Produksi Finishing" => '120363379456107274',
            "Pabrik Banyuwangi" => '120363377878364167',
            "QC Banyuwangi" => '120363377473145803',
            "Produksi Fabrikasi" => '120363380070486933',
            "Teknologi Banyuwangi" => '120363380004952568',
            "Shop Drawing" => '120363381781364599',
            "PPO Banyuwangi" => '120363381499645393',
            "Sinkron SAP" => '120363401548797236',
            "Finishing Bogie" => '120363416642047329',
            "Fabrikasi Bogie" => '120363403029395737',
        ];

        // Initialize an array to store phone numbers
        $numbers = [];

        if (array_key_exists($unit, $unitNumbers)) {
            $numbers[] = $unitNumbers[$unit];
        } else {
            $users = User::where('rule', $unit)->get();
            foreach ($users as $user) {
                $numbers[] = $user->waphonenumber;
            }
        }

        return self::sendWaMessage($numbers, $message);
    }
}
