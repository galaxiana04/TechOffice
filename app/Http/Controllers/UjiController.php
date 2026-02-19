<?php

namespace App\Http\Controllers;
use App\Models\TelegramMessagesAccount;
use Illuminate\Support\Facades\Storage;
use App\Models\Category;
use App\Models\Jobticket;
use App\Models\JobticketStartedRev;
use App\Models\Newprogressreporthistory;
use App\Models\JobticketHistory;
use App\Models\Newprogressreport;
use App\Models\User;
use App\Models\CollectFile;
use App\Models\JobticketIdentity;
use App\Models\JobticketPart;
use App\Models\ProjectType;
use App\Models\JobticketDocumentKind;
use App\Models\Unit;
use App\Imports\ColumnAImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Imports\RawprogressreportsImport;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class UjiController extends Controller
{

    public function download()
    {
        // Login ke AutodeskTC
        $login = Http::asForm()->post('http://10.10.0.40/AutodeskTC/login', [
            'username' => 'aditya.tatmaja',
            'password' => '12345678',
        ]);

        // Cek apakah login berhasil
        if (!$login->successful()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal login ke AutodeskTC',
                'debug' => $login->body()
            ], 401);
        }

        // Ambil cookies session dari response login
        $cookies = $login->cookies();

        // Lakukan request download file
        $download = Http::withCookies($cookies->toArray(), '10.10.0.40')
            ->get('http://10.10.0.40/AutodeskTC/10.10.0.40/TekVault_0003_Dec2011/Document/Download', [
                'fileId' => 'mRklMRTpMQVRFU1Q6OTMxOTEwMA',
                'downloadAsInline' => 'true',
            ]);

        // Jika gagal download
        if (!$download->successful()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengunduh dokumen',
                'debug' => $download->body()
            ], 400);
        }

        // Kirim file sebagai response download ke user
        return response($download->body(), 200)
            ->header('Content-Type', $download->header('Content-Type', 'application/pdf'))
            ->header('Content-Disposition', 'attachment; filename="dokumen.pdf"');
    }

    public function updatesupportdocument(Request $request, $jobticketid)
    {
        // Temukan jobticket berdasarkan ID
        $jobticket = Jobticket::find($jobticketid);

        // Pastikan dokumen support ada dalam request
        if ($request->has('documentsupport')) {
            $datas = $request->documentsupport;

            // Buat list ID dokumen yang dipilih
            $listid = [];
            foreach ($datas as $data) {
                $listItem = explode('@', $data); // Gunakan explode untuk memecah string
                $listid[] = $listItem[0]; // Ambil ID dokumen dari elemen pertama
            }

            // Langkah 1: Hapus semua referensi jobticketid dari newprogressreport
            $reportsWithJobticket = Newprogressreport::whereJsonContains('jobticketids', $jobticketid)->get();
            foreach ($reportsWithJobticket as $report) {
                // Ambil array jobticketids dari JSON
                $data = json_decode($report->jobticketids, true);
                if (($key = array_search($jobticketid, $data)) !== false) {
                    // Hapus jobticketid dari array
                    unset($data[$key]);
                }
                // Simpan kembali array yang sudah dihapus jobticketid-nya
                $report->jobticketids = json_encode(array_values($data)); // array_values untuk reset index
                $report->save();
            }

            // Langkah 2: Simpan dokumen support dalam bentuk JSON pada jobticket
            $jobticket->documentsupport = json_encode($listid);
            $jobticket->save(); // Simpan perubahan ke database

            // Ambil dokumen yang sesuai dengan ID yang telah dipilih untuk dikembalikan
            $documents = Newprogressreporthistory::whereIn('id', $listid)
                ->select('id', 'namadokumen', 'rev', 'newprogressreport_id') // pastikan ambil relasi
                ->get();

            // Langkah 3: Tambahkan kembali jobticketid ke newprogressreport yang sesuai
            foreach ($documents as $document) {
                // Akses relasi newProgressReport
                $newprogressreport = $document->newProgressReport;

                // Ambil data jobticketids sebagai array
                $data = json_decode($newprogressreport->jobticketids, true);
                if (!is_array($data)) {
                    $data = []; // Pastikan $data adalah array
                }

                // Tambahkan jobticketid ke array, pastikan tidak ada duplikasi
                if (!in_array($jobticketid, $data)) {
                    $data[] = $jobticketid;
                }

                // Simpan kembali jobticketids dalam bentuk JSON
                $newprogressreport->jobticketids = json_encode($data);
                $newprogressreport->save(); // Simpan perubahan
            }

            // Kirim respons JSON dengan data dokumen yang diperbarui
            return response()->json([
                'success' => true,
                'message' => 'Dokumen support berhasil diperbarui',
                'data' => $documents
            ]);
        } else {
            // Jika tidak ada dokumen support yang dikirim
            return response()->json([
                'success' => false,
                'message' => 'Dokumen support tidak ditemukan dalam permintaan',
            ], 400);
        }
    }


    






    






}
