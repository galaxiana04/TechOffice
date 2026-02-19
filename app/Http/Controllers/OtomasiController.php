<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;
use App\Models\RespondedMessage;
use App\Jobs\DownloadAutodeskPdfJob;
use App\Models\CollectFile;
use App\Models\KatalogKomat;
use App\Models\Newbomkomat;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\FileController;
use Telegram\Bot\FileUpload\InputFile;
use Telegram\Bot\Laravel\Facades\Telegram;
use Spatie\DbDumper\Databases\MySql;


class OtomasiController extends Controller
{
    protected $fileController;
    protected $progressreportController;
    protected $bottelegramController;

    public function __construct(FileController $fileController, ProgressreportController $progressreportController, BotTelegramController $bottelegramController)
    {
        $this->fileController = $fileController;
        $this->progressreportController = $progressreportController;
        $this->bottelegramController = $bottelegramController;
    }
    private $telegram_api_url = 'https://api.telegram.org/bot6932879805:AAGcZyniuYjiP7m29xg7EDXJjztZRCxc378/';

    public function getUpdatesTelegramCommand()
    {
        $response = Http::get($this->telegram_api_url . 'getUpdates');
        $updates  = $response->json()['result'] ?? [];
        $token  = "6932879805:AAGcZyniuYjiP7m29xg7EDXJjztZRCxc378";
        $processed = 0;
        $success   = 0;
        $failed    = 0;
        $details   = [];

        foreach ($updates as $update) {
            if (!isset($update['message'])) {
                continue;
            }

            $message     = $update['message'];
            $message_id  = $message['message_id'] ?? null;
            $chat_id     = $message['chat']['id'] ?? null;
            $date        = $message['date'] ?? null;
            $text        = $message['text'] ?? '';

            if (!$message_id || !$chat_id || !$date || !$text) {
                continue;
            }

            // --- Cek duplikat ---
            $existingMessage = RespondedMessage::where('message_id', $message_id)->first();
            if ($existingMessage) {
                continue; // sudah dibalas, skip
            }

            $replyText = null;
            $keyword   = null;

            if (env('APP_NAME') == 'inka_local') {
                if (stripos($text, 'Downloadfile_') === 0) {


                    $id = substr($text, 13); // karena "Downloadfile_" panjangnya 13

                    $file = CollectFile::find($id);

                    if (!$file) {
                        $replyText = "⚠️ File dengan ID *$id* tidak ditemukan.";
                        $keyword   = "downloadfile";
                    } else {
                        // Path file sebenarnya
                        $path = storage_path("app/public/uploads/" . $file->filename);

                        if (!file_exists($path)) {
                            $replyText = "⚠️ File fisik tidak ditemukan: {$file->filename}";
                        } else {
                            // Kirim file ke Telegram
                            $response = Http::attach(
                                'document',
                                file_get_contents($path),
                                $file->filename
                            )->post("https://api.telegram.org/bot{$token}/sendDocument", [
                                'chat_id' => $chat_id
                            ]);

                            // Jangan overwrite $response
                            $body = $response->json();

                            if ($response->successful() && ($body['ok'] ?? false) === true) {
                                $replyText = "📁 File *{$file->filename}* berhasil dikirim!";
                            } else {
                                $replyText = "⚠️ Gagal mengirim file.\n\nError: " . json_encode($body);
                            }
                        }

                        $keyword = "downloadfile";
                    }
                } elseif (stripos($text, 'Downloaddokumen_') === 0) {
                    $nodokumen = substr($text, 16);
                    $keyword   = "downloaddokumen";

                    // Langsung kasih feedback cepat ke user
                    $replyText = "Mencari dokumen *$nodokumen*...\nSilakan tunggu, file PDF Autodesk sedang diproses (bisa 10–30 detik).";

                    // Lempar ke background job
                    DownloadAutodeskPdfJob::dispatch($nodokumen, $chat_id, $token);

                    // Bot langsung balas cepat, proses lanjut di background
                }
            } else {
                // --- Handler Keyword ---
                if (stripos($text, 'cekkomat_') === 0) {
                    $query = substr($text, 9);

                    $results = KatalogKomat::where('kodematerial', 'LIKE', '%' . $query . '%')
                        ->orWhere('deskripsi', 'LIKE', '%' . $query . '%')
                        ->orWhere('spesifikasi', 'LIKE', '%' . $query . '%')
                        ->get();

                    $kodematerials = $results->pluck('kodematerial');

                    $newbomkomats = Newbomkomat::with('newbom.projectType')
                        ->whereIn('kodematerial', $kodematerials)
                        ->get();

                    $proyekData = [];
                    foreach ($newbomkomats as $newbomkomat) {
                        $title = $newbomkomat->newbom->projectType->title;
                        if (!isset($proyekData[$newbomkomat->kodematerial])) {
                            $proyekData[$newbomkomat->kodematerial] = [];
                        }
                        if (!in_array($title, $proyekData[$newbomkomat->kodematerial])) {
                            $proyekData[$newbomkomat->kodematerial][] = $title;
                        }
                    }

                    $textResult = "";

                    if ($results->count() > 0) {
                        $latestUpdate = $results->max('created_at')->format('d/m/Y');
                        $textResult .= "🔍 *Hasil Pencarian untuk:* _" . $query . "_\n\n";
                        $textResult .= "📅 *Update terakhir:* _" . $latestUpdate . "_\n\n";
                        $textResult .= "🏢 *Plant :* _2100 INKA MADIUN_\n\n";
                    }

                    foreach ($results as $result) {
                        $textResult .= "📝 *Kode Material*: " . $result->kodematerial . "\n";
                        $textResult .= "📋 *Deskripsi*: " . $result->deskripsi . "\n";
                        $textResult .= "📊 *Spesifikasi*: " . $result->spesifikasi . "\n";
                        $textResult .= "📦 *UoM*: " . $result->UoM . "\n";
                        $textResult .= "📈 *Stok UU di Ekspedisi*: " . $result->stokUUekpedisi . "\n";
                        $textResult .= "📦 *Stok UU di Gudang*: " . $result->stokUUgudang . "\n";
                        $textResult .= "📊 *Stok Project di Ekspedisi*: " . $result->stokprojectekpedisi . "\n";
                        $textResult .= "📦 *Stok Project di Gudang*: " . $result->stokprojectgudang . "\n";

                        if (isset($proyekData[$result->kodematerial])) {
                            $textResult .= "🌍 *Terikat dengan proyek:*\n";
                            foreach ($proyekData[$result->kodematerial] as $proyek) {
                                $textResult .= "- " . $proyek . "\n";
                            }
                        } else {
                            $textResult .= "⚠️ Tidak terikat dengan proyek mana pun.\n";
                        }

                        $textResult .= "----------------------------------\n\n";
                    }

                    if (empty($textResult)) {
                        $textResult = "⚠️ Tidak ada katalog komat yang ditemukan untuk pencarian: *" . $query . "*";
                    }

                    $replyText = $textResult;
                    $keyword   = "cekkomat";
                } else if (stripos($text, 'cek_telegramid') === 0) {
                    $replyText = "🤖 ID Telegram Anda adalah: *" . $chat_id . "*\n\n" .
                        "Silakan simpan ID ini untuk keperluan pendaftaran di sistem INKA.";
                    $keyword   = "mytelegramid";
                } elseif (stripos($text, 'help') === 0 || stripos($text, '/start') === 0) {
                    $replyText = "Selamat datang di *Bot INKA Madiun*  

                        Berikut perintah yang bisa kamu gunakan:\n\n" .
                        "1. `cekmemo_<nomor_memo>`  \n   → Cek status & detail memo\n   Contoh: `cekmemo_12345678`\n\n" .
                        "2. `cekkomat_<kata_kunci>`  \n   → Cari katalog material (kode/deskripsi/spesifikasi)\n   Contoh: `cekkomat_baut M10`\n\n" .
                        "3. `Downloadfile_<id>`  \n   → Download file yang di-upload ke bot (hanya untuk user internal)\n   Contoh: `Downloadfile_45`\n\n" .
                        "4. `Downloaddokumen_<nomor_dokumen>`  \n   → Download file PDF Autodesk terbaru dari dokumen tersebut\n   Contoh: `Downloaddokumen_INKA-2025-001`\n\n" .
                        "5. `cek_telegramid`  \n   → Tampilkan Telegram ID kamu (untuk registrasi)\n\n" .
                        "6. `help` atau `/start`  \n   → Menampilkan pesan bantuan ini\n\n" .
                        "Link pencarian katalog lengkap:\nhttps://inka.goovicess.com/katalogkomat/search\n\n" .
                        "Info lebih lanjut hubungi admin divisi terkait. Terima kasih!";

                    $keyword = "help";
                }
            }


            if ($replyText !== null) {
                $processed++;
                try {
                    if (strlen($replyText) <= 4000) {
                        // ✅ Aman, kirim langsung
                        $this->sendMessage($chat_id, $replyText);
                    } else {
                        $replyText = substr($replyText, 0, 3800) . " ...";
                        $this->sendMessage($chat_id, $replyText . ". Selengkapnya di tech office: " . 'https://inka.goovicess.com/katalogkomat/search?query=' . urlencode($query));
                    }

                    $success++;
                    $details[] = [
                        'message_id' => $message_id,
                        'chat_id'    => $chat_id,
                        'keyword'    => $keyword,
                        'status'     => 'success'
                    ];
                } catch (\Exception $e) {
                    $failed++;
                    $details[] = [
                        'message_id' => $message_id,
                        'chat_id'    => $chat_id,
                        'keyword'    => $keyword,
                        'status'     => 'failed',
                        'error'      => $e->getMessage()
                    ];
                }

                // --- Simpan pesan yang sudah direspon ---
                RespondedMessage::create([
                    'message_id' => $message_id,
                    'chat_id'    => $chat_id,
                    'date'       => date('Y-m-d H:i:s', $date)
                ]);
            }
        }

        return response()->json([
            'processed' => $processed,
            'success'   => $success,
            'failed'    => $failed,
            'details'   => $details
        ]);
    }


    private function sendMessage($chat_id, $text)
    {
        $token = "6932879805:AAGcZyniuYjiP7m29xg7EDXJjztZRCxc378"; // sebaiknya simpan di .env
        $url   = "https://api.telegram.org/bot{$token}/sendMessage";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, [
            "chat_id" => $chat_id,
            "text"    => $text,
        ]);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
        }

        curl_close($ch);

        return $response;
    }


    public function run_simpandatabaseinka()
    {
        $host = config('inka.DB_HOST');
        $port = config('inka.DB_PORT');
        $database = config('inka.DB_DATABASE');
        $username = config('inka.DB_USERNAME');
        $password = config('inka.DB_PASSWORD');

        // Direktori untuk menyimpan file backup
        $backupDirectory = storage_path('app/public/backupsdatabase');

        // Buat folder pencadangan jika belum ada
        if (!file_exists($backupDirectory)) {
            mkdir($backupDirectory, 0777, true);
        }

        // Cek dan hapus file backup lama jika lebih dari 5
        $files = glob($backupDirectory . '/*.sql'); // Mengambil semua file .sql di direktori backup
        if (count($files) >= 5) {
            // Urutkan file berdasarkan waktu (terbaru pertama)
            usort($files, function ($a, $b) {
                return filemtime($b) - filemtime($a); // Membandingkan berdasarkan waktu modifikasi file
            });

            // Hapus file yang lebih lama dari 5 file terbaru
            $filesToDelete = array_slice($files, 5); // Ambil file yang lebih dari 5
            foreach ($filesToDelete as $file) {
                unlink($file); // Menghapus file backup lama
            }
        }

        // Nama file pencadangan
        $backupFileName = 'inka_' . date('Y-m-d_H-i-s') . '.sql';
        $backupFilePath = $backupDirectory . '/' . $backupFileName; // Simpan di direktori yang benar

        try {
            MySql::create()
                ->setDbName($database)
                ->setUserName($username)
                ->setPassword($password)
                ->setHost($host)
                ->setPort($port)
                ->dumpToFile($backupFilePath);

            return response()->json(['success' => 'Backup created successfully.']);
        } catch (\Exception $e) {

            return response()->json(['error' => 'Backup failed.'], 500);
        }
    }

    public function download_last_backup()
    {
        // Direktori untuk file backup
        $backupDirectory = storage_path('app/public/backupsdatabase');

        // Ambil semua file .sql di direktori backup
        $files = glob($backupDirectory . '/*.sql');

        // Periksa apakah ada file backup
        if (count($files) == 0) {
            return response()->json(['error' => 'No backups found.'], 404);
        }

        // Urutkan file berdasarkan waktu (terbaru pertama)
        usort($files, function ($a, $b) {
            return filemtime($b) - filemtime($a); // Membandingkan berdasarkan waktu modifikasi file
        });

        // Ambil file backup terbaru
        $latestBackup = $files[0];

        // Set header untuk mendownload file
        return response()->download($latestBackup);
    }


    public function showLogs()
    {
        $path = storage_path('logs/laravel.log'); // atau laravel-2025-09-21.log
        if (!File::exists($path)) {
            return 'Log file tidak ditemukan';
        }

        $lines = collect(explode("\n", File::get($path)))
            ->filter()->slice(-200)->implode("\n");

        return response(nl2br(e($lines)));
    }
}
