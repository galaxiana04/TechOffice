<?php

namespace App\Http\Controllers;

use App\Models\ProductBreakdownStructure;
use App\Models\ProjectType;
use App\Models\Newprogressreport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class ProductBreakdownStructureController extends Controller
{
    /**
     * Menampilkan halaman utama PBS.
     */
    public function index()
    {
        $projectTypes = ProjectType::where('id', '!=', 1)->get();

        // Mengambil daftar progress report tertentu untuk kebutuhan modal attach
        $newprogressreports = Newprogressreport::whereIn('newreport_id', [73, 76, 132, 169, 191, 200, 207, 209, 270, 988, 1218, 1243])->get();

        return view('product_breakdown_structures.index', compact('projectTypes', 'newprogressreports'));
    }

    public function getData($projectId)
    {
        try {
            Log::info('Fetching PBS data for project ID: ' . $projectId);

            $pbsItems = ProductBreakdownStructure::with(['newProgressReports', 'projectType'])
                ->where('project_type_id', $projectId)
                ->join('project_types', 'product_breakdown_structures.project_type_id', '=', 'project_types.id')
                ->select('product_breakdown_structures.*', 'project_types.title as project_title')
                // Urutkan berdasarkan ID ASC (mengikuti urutan di CSV/Excel)
                ->orderBy('product_breakdown_structures.id', 'ASC')
                ->get();

            Log::info('Found ' . $pbsItems->count() . ' PBS records for project ' . $projectId);

            $data = [];
            foreach ($pbsItems as $item) {
                // Gunakan kolom product sebagai kode hierarki (contoh: R35-KRL-1.1)
                $currentCode = $item->product;

                // Tentukan level depth berdasarkan kolom level yang terisi
                $levelDepth = 1;
                if (!empty($item->level4)) {
                    $levelDepth = 4;
                } elseif (!empty($item->level3)) {
                    $levelDepth = 3;
                } elseif (!empty($item->level2)) {
                    $levelDepth = 2;
                }

                // Tentukan parent dari kode produk
                // Contoh: R35-KRL-1.1 -> parentnya R35-KRL-1
                $parentCode = 'root';
                $lastDot = strrpos($currentCode, '.');
                if ($lastDot !== false) {
                    $parentCode = substr($currentCode, 0, $lastDot);
                }

                $itemData = $item->toArray();

                $data[] = array_merge($itemData, [
                    'code' => $currentCode,
                    'parent' => $parentCode,
                    'level_depth' => $levelDepth,
                    'source_drawing_names' => $item->newProgressReports->map(function ($report) {
                        return [
                            'id' => $report->id,
                            'nodokumen' => $report->nodokumen,
                            'namadokumen' => $report->namadokumen,
                        ];
                    })->toArray()
                ]);
            }

            return response()->json($data);
        } catch (\Exception $e) {
            Log::error('Error in getData method: ' . $e->getMessage());
            return response()->json([
                'error' => true,
                'message' => 'Error fetching data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Menampilkan semua referensi PBS dari semua project.
     */
    public function indexallreference()
    {
        $projectTypes = ProjectType::where('id', '!=', 1)->get();
        $newprogressreports = Newprogressreport::whereIn('newreport_id', [73, 76, 132, 169, 191, 200, 207, 209, 270, 988, 1218, 1243])->get();

        // Menggunakan view yang sama tapi mungkin dengan flag atau data tambahan jika diperlukan
        // Namun biasanya user ingin halaman terpisah yang menampilkan semua tanpa filter project di awal
        return view('product_breakdown_structures.index_all', compact('projectTypes', 'newprogressreports'));
    }

    /**
     * Mengambil semua data PBS dari semua project untuk tampilan All Reference.
     */
    public function getAllData()
    {
        try {
            $pbsItems = ProductBreakdownStructure::with(['newProgressReports', 'projectType'])
                ->join('project_types', 'product_breakdown_structures.project_type_id', '=', 'project_types.id')
                ->select('product_breakdown_structures.*', 'project_types.title as project_title')
                // 1. Urutkan berdasarkan Title Project (KRL)
                ->orderBy('project_types.title', 'ASC')
                // 2. Urutan hierarki numerik
                ->orderByRaw('CAST(level1 AS DECIMAL(10,2)) ASC')
                ->orderByRaw('CASE WHEN level2 IS NULL THEN 0 ELSE 1 END')
                ->orderByRaw('CAST(level2 AS DECIMAL(10,2)) ASC')
                ->orderByRaw('CASE WHEN level3 IS NULL THEN 0 ELSE 1 END')
                ->orderByRaw('CAST(level3 AS DECIMAL(10,2)) ASC')
                ->orderByRaw('CASE WHEN level4 IS NULL THEN 0 ELSE 1 END')
                ->orderByRaw('CAST(level4 AS DECIMAL(10,2)) ASC')
                ->get();

            $data = [];
            foreach ($pbsItems as $item) {
                $codeParts = array_values(array_filter([$item->level1, $item->level2, $item->level3, $item->level4], function ($val) {
                    return !is_null($val) && $val !== '';
                }));

                $levelDepth = count($codeParts);
                // Tambahkan ID project ke kode agar unik saat digabung antar project
                $currentCode = $item->project_type_id . '-' . implode('.', $codeParts);

                $parentCode = 'root';
                if ($levelDepth > 1) {
                    $parentParts = array_slice($codeParts, 0, -1);
                    $parentCode = $item->project_type_id . '-' . implode('.', $parentParts);
                }

                $itemData = $item->toArray();

                $data[] = array_merge($itemData, [
                    'code' => $currentCode,
                    'parent' => $parentCode,
                    'level_depth' => $levelDepth,
                    'source_drawing_names' => $item->newProgressReports->map(function ($report) {
                        return [
                            'id' => $report->id,
                            'nodokumen' => $report->nodokumen,
                            'namadokumen' => $report->namadokumen,
                        ];
                    })->toArray()
                ]);
            }

            return response()->json($data);
        } catch (\Exception $e) {
            Log::error('Error in getAllData method: ' . $e->getMessage());
            return response()->json(['error' => true, 'message' => $e->getMessage()], 500);
        }
    }

    public function attachnewreportprogressandrealibiltyallocation(Request $request)
    {
        $request->validate([
            'product_breakdown_structure_id' => 'required|exists:product_breakdown_structures,id',
            'newprogressreport_id' => 'required|exists:newprogressreports,id',
        ]);

        $pbs = ProductBreakdownStructure::findOrFail($request->reliability_allocation_id);
        $pbs->newProgressReports()->syncWithoutDetaching([$request->newprogressreport_id]);

        return response()->json(['success' => true]);
    }

    /**
     * Menghapus (Detach) relasi antara PBS dan Dokumen.
     */
    public function detachnewreportprogressandrealibiltyallocation($pbsId, $reportId)
    {
        $pbs = ProductBreakdownStructure::findOrFail($pbsId);
        $pbs->newProgressReports()->detach($reportId);

        return response()->json(['success' => true]);
    }

    /**
     * API untuk debugging kolom tabel.
     */
    public function getTableColumns()
    {
        try {
            $columns = DB::select("SHOW COLUMNS FROM product_breakdown_structures");
            $columnNames = array_map(fn($column) => $column->Field, $columns);

            return response()->json([
                'success' => true,
                'columns' => $columnNames
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => true, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Upload file Excel/CSV untuk mengimport data PBS.
     * Data lama untuk project yang dipilih akan dihapus terlebih dahulu.
     */
    public function uploadExcel(Request $request)
    {
        $request->validate([
            'project_type_id' => 'required|exists:project_types,id',
            'excel_file' => 'required|file|mimes:xlsx,xls,csv,txt'
        ]);

        try {
            $projectTypeId = $request->project_type_id;

            // Hapus data PBS lama untuk project ini
            ProductBreakdownStructure::where('project_type_id', $projectTypeId)->delete();

            // Baca file
            $file = $request->file('excel_file');
            $extension = strtolower($file->getClientOriginalExtension());

            $rows = [];

            if ($extension === 'csv' || $extension === 'txt') {
                // Baca file CSV
                $handle = fopen($file->getPathname(), 'r');
                while (($data = fgetcsv($handle, 1000, ',')) !== FALSE) {
                    $rows[] = $data;
                }
                fclose($handle);
            } else {
                // Baca file Excel
                $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getPathname());
                $worksheet = $spreadsheet->getActiveSheet();
                $rows = $worksheet->toArray();
            }

            // Skip header row (row pertama)
            $header = array_shift($rows);

            $insertedCount = 0;
            foreach ($rows as $row) {
                // Skip baris kosong
                if (empty(array_filter($row))) continue;

                // Format CSV: project_type_id (code), level1, level2, level3, level4
                ProductBreakdownStructure::create([
                    'project_type_id' => $projectTypeId,
                    'product' => trim($row[0] ?? ''),  // Kolom project_type_id di CSV berisi kode produk
                    'level1' => trim($row[1] ?? ''),
                    'level2' => trim($row[2] ?? ''),
                    'level3' => trim($row[3] ?? ''),
                    'level4' => trim($row[4] ?? ''),
                ]);
                $insertedCount++;
            }

            Log::info("PBS Excel/CSV upload successful. Project ID: $projectTypeId, Inserted: $insertedCount rows");

            return redirect()->route('product-breakdown-structure.index')
                ->with('success', "Berhasil mengimport $insertedCount data PBS dari file.");
        } catch (\Exception $e) {
            Log::error('Error uploading PBS file: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal mengimport file: ' . $e->getMessage());
        }
    }

    /**
     * Menghapus semua data PBS untuk project tertentu.
     */
    public function destroyAll(Request $request)
    {
        $request->validate([
            'project_type_id' => 'required|exists:project_types,id',
        ]);

        try {
            $projectTypeId = $request->project_type_id;

            // Hapus semua data PBS untuk project ini
            $deletedCount = ProductBreakdownStructure::where('project_type_id', $projectTypeId)->delete();

            Log::info("PBS bulk delete for project ID: $projectTypeId. Deleted: $deletedCount records.");

            return response()->json([
                'success' => true,
                'message' => "Berhasil menghapus $deletedCount data PBS."
            ]);
        } catch (\Exception $e) {
            Log::error('Error deleting PBS data: ' . $e->getMessage());
            return response()->json([
                'error' => true,
                'message' => 'Gagal menghapus data: ' . $e->getMessage()
            ], 500);
        }
    }
}
