<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use App\Models\User;

class TelegramService
{
    public static function sendTeleMessage($listchatid, $pesan)
    {
        $token  = "6932879805:AAGcZyniuYjiP7m29xg7EDXJjztZRCxc378";

        // pastikan chat id valid
        $formattedIds = array_filter($listchatid, fn($id) => is_numeric($id));

        if (empty($formattedIds)) {
            return response()->json([
                'message' => 'Tidak ada chat id yang valid'
            ], 400);
        }

        $results = [];
        foreach ($formattedIds as $chatid) {
            $response = Http::post("https://api.telegram.org/bot{$token}/sendMessage", [
                'chat_id' => $chatid,
                'text'    => $pesan,
            ]);

            $results[] = [
                'chat_id' => $chatid,
                'status'  => $response->status(),
                'body'    => $response->json(),
            ];
        }

        return response()->json([
            'message' => 'Proses kirim selesai',
            'results' => $results
        ], 200);
    }

    public static function ujisendunit($unit, $message)
    {
        $unitChatIds = [
            "Quality Engineering"          => -4147225904,
            "Electrical Engineering System" => -4167786260,
            "Mechanical Engineering System" => -4119524947,
            "Product Engineering"          => -4198400936,
            "Desain Mekanik & Interior"    => -4185377771,
            "Desain Carbody"               => -4109358061,
            "RAMS"                         => -4179548004,
            "Desain Bogie & Wagon"         => -4177114589,
            "Desain Elektrik"              => -4110317119,
            "Preparation & Support"        => -4149864532,
            "Welding Technology"           => -4132751208,
            "Teknologi Proses"             => -4108868680,
            "Shop Drawing"                 => -4133766074,
            "PPO"                          => -4531388107,
            "Produksi Fabrikasi"           => -4536862809,
            "Produksi Finishing"           => -4538506305,
            "QC Banyuwangi"                => -4728957970,
            "PPO Banyuwangi"               => -4981056058,
            "Fabrikasi Bogie"              => -4960571184,
            "Teknologi Banyuwangi"         => -4905126763,
            "Pabrik Banyuwangi"            => -4666771938,
            "QC INC"                       => -4857826008,
            "Sinkron SAP"                  => -4976906511,
        ];

        $chatids = [];

        if (array_key_exists($unit, $unitChatIds)) {
            $chatids[] = $unitChatIds[$unit];
        } else {
            $users = User::where('rule', $unit)->get();
            foreach ($users as $user) {
                if ($user->telegram_chatid) {
                    $chatids[] = $user->telegram_chatid;
                }
            }
        }

        return self::sendTeleMessage($chatids, $message);
    }
}
