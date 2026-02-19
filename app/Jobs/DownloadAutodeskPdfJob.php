<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use App\Models\Newprogressreport;

class DownloadAutodeskPdfJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 120;      // Maksimal 2 menit
    public $tries   = 2;        // Coba ulang 2 kali kalau gagal

    protected $nodokumen;
    protected $chat_id;
    protected $token;

    public function __construct($nodokumen, $chat_id, $token)
    {
        $this->nodokumen = $nodokumen;
        $this->chat_id   = $chat_id;
        $this->token     = $token;
    }

    public function handle()
    {
        $newprogressreport = Newprogressreport::with('histories')
            ->where('nodokumen', $this->nodokumen)
            ->first();

        if (!$newprogressreport) {
            $this->sendMessage("Dokumen dengan nomor *{$this->nodokumen}* tidak ditemukan.");
            return;
        }

        $history = $newprogressreport->histories
            ->whereNotNull('fileid')
            ->sortByDesc('created_at')
            ->first();

        if (!$history || !$history->fileid) {
            $this->sendMessage("Tidak ada file Autodesk pada dokumen *{$this->nodokumen}*.");
            return;
        }

        $fileId = $history->fileid;

        // Ambil file dari Node.js
        $response = Http::timeout(90)->get("http://192.168.13.160:3000/download-autodesk", [
            'fileId' => $fileId
        ]);

        if ($response->failed()) {
            $this->sendMessage("Gagal mengambil file dari server Autodesk.\nError: " . $response->body());
            return;
        }

        $fileContent = $response->body();
        $fileName    = "autodesk_{$fileId}.pdf";

        // Kirim ke Telegram
        $send = Http::attach(
            'document',
            $fileContent,
            $fileName
        )->post("https://api.telegram.org/bot{$this->token}/sendDocument", [
            'chat_id' => $this->chat_id,
        ]);

        $result = $send->json();

        if ($send->successful() && ($result['ok'] ?? false)) {
            $this->sendMessage("File PDF Autodesk berhasil dikirim!\n*{$fileName}*");
        } else {
            $error = json_encode($result);
            $this->sendMessage("Gagal mengirim file ke Telegram.\nError: {$error}");
        }
    }

    protected function sendMessage($text)
    {
        Http::post("https://api.telegram.org/bot{$this->token}/sendMessage", [
            'chat_id'    => $this->chat_id,
            'text'       => $text,
            'parse_mode' => 'Markdown'
        ]);
    }
}
