<?php

namespace App\Jobs;

use Illuminate\Bus\Batchable; // ✅ Tambahkan ini
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Models\CollectFile;

class DownloadProgressPdfJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, Batchable, SerializesModels;

    public $fileid;
    public $collectableType;
    public $collectableId;

    public function __construct($fileid, $collectableType, $collectableId)
    {
        $this->fileid = $fileid;
        $this->collectableType = $collectableType;
        $this->collectableId = $collectableId;
    }

    public function handle(): void
    {
        try {
            
            $fileurl = 'http://192.168.13.160:3000/download-autodesk?fileId='. $this->fileid;

            $response = Http::timeout(120)->get($fileurl);

            if ($response->successful()) {
                // Ambil nama file dari header Content-Disposition jika tersedia
                $disposition = $response->header('Content-Disposition');
                $originalFilename = $this->fileid . '.pdf'; // default

                if ($disposition && preg_match('/filename[^;=\n]*=((["\']).*?\2|[^;\n]*)/', $disposition, $matches)) {
                    $originalFilename = trim($matches[1], '"');
                }

                // Cek jika filename sudah ada di DB
                $filename = $originalFilename;
                $count = 1;
                while (CollectFile::where('filename', $filename)->exists()) {
                    $filename = pathinfo($originalFilename, PATHINFO_FILENAME) . "_$count." . pathinfo($originalFilename, PATHINFO_EXTENSION);
                    $count++;
                }

                $path = 'uploads/' . $filename;
                Storage::put('public/' . $path, $response->body());

                // Simpan ke DB
                CollectFile::create([
                    'filename' => $filename,
                    'link' => $path,
                    'collectable_type' => $this->collectableType,
                    'collectable_id' => $this->collectableId,
                ]);
                // Tandai sebagai sudah didownload
                $modelClass = $this->collectableType;
                $model = $modelClass::find($this->collectableId);
                if ($model) {
                    $model->isdownloaded = true;
                    $model->save();
                }
            }
        } catch (\Exception $e) {
            logger()->error("Download PDF gagal (fileid {$this->fileid}): " . $e->getMessage());
        }
    }
}
