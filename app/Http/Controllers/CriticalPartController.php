<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Fmeca;
use App\Models\FmecaIdentity;
use App\Services\TelegramService;
use App\Models\ProjectType;
use App\Models\CollectFile;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class CriticalPartController extends Controller
{

    public function upload()
    {
        // Display the main page for FMECA
        return view('fmeca.upload');
    }



    public function show($fmeca_identity_id)
    {
        $allproject = ProjectType::all(); // Ensure ProjectType is loaded if needed
        $fmeca_identity = FmecaIdentity::with('fmecas.projectType')->findOrFail($fmeca_identity_id); // Corrected method to findOrFail
        $file = CollectFile::where('collectable_id', $fmeca_identity->id)
            ->where('collectable_type', FmecaIdentity::class)
            ->first(); // Fetch files related to the FMECA identity
        return view('fmeca.show', compact('allproject', 'fmeca_identity', 'file'));
    }

    public function uploadexcell(Request $request)
    {
        $this->validateExcel($request);
        // Validate the structure of the Excel file
        $projectname = ProjectType::pluck('title', 'id')->all();
        // Process the uploaded Excel file
        $importedData = $this->importExcel($request->file('excel_file'));
        $this->validateExcelStructure($importedData); // Tambahkan validasi
        $revisiData = [];
        DB::beginTransaction();
        $userName = auth()->user()->name; // Get the current user's name

        try {

            $project_type_id = null;
            foreach ($importedData as $sheetname => $sheetData) {
                $sheetRevisiData = $this->progressreportexported($sheetData, $sheetname);
                if (empty($project_type_id)) {
                    $project_type_id = $sheetRevisiData[0]['project_type_id'] ?? null;
                    if (empty($project_type_id)) {
                        throw new \Exception("Failed to determine project_type_id for sheet '$sheetname'.");
                    }
                }
            }
            $projectTitle = $projectname[$project_type_id] ?? 'Unknown Project';
            $fmecaidentity = FmecaIdentity::create([
                'user_id' => auth()->id(),
                'project_type_id' => $project_type_id
            ]);
            $uploadedFile = $request->file('excel_file');
            // Dapatkan nama file yang diunggah 
            $filename = $uploadedFile->getClientOriginalName();

            // Dapatkan ekstensi file
            $fileFormat = $uploadedFile->getClientOriginalExtension();

            // Hapus ekstensi file dari nama file
            $filenameWithoutExtension = pathinfo($filename, PATHINFO_FILENAME);

            // Gabungkan nama file (tanpa ekstensi), nama pengguna, dan format file
            $filenameWithUserAndFormat = $filenameWithoutExtension . '_' . $userName . '.' . $fileFormat;

            // Sekarang, $filenameWithUserAndFormat berisi nama file yang dihasilkan dengan nama pengguna dan format file
            $filename = $filenameWithUserAndFormat;

            // Periksa apakah nama file sudah ada
            $count = 0;
            $newFilename = $filename;
            while (CollectFile::where('filename', $newFilename)->exists()) {
                $count++;
                $newFilename = pathinfo($filename, PATHINFO_FILENAME) . '_' . $count . '.' . pathinfo($filename, PATHINFO_EXTENSION);
            }

            // Simpan file di storage/app/public/uploads
            $path = $uploadedFile->storeAs('public/uploads', $newFilename);

            // Simpan file terkait di database
            $fmecaFile = new CollectFile();
            $fmecaFile->filename = $newFilename;
            $fmecaFile->link = str_replace('public/', '', $path);
            $fmecaFile->collectable_id = $fmecaidentity->id; // Menghubungkan file dengan FMECA identity
            $fmecaFile->collectable_type = FmecaIdentity::class; // Tipe polimorfik
            $fmecaFile->save();
            foreach ($importedData as $sheetname => $sheetData) {
                // Call the progress report exported function for each sheet
                $sheetRevisiData = $this->progressreportexported($sheetData, $sheetname);

                foreach ($sheetRevisiData as $data) {
                    $data['fmeca_identity_id'] = $fmecaidentity->id; // Associate the identity with the FMECA entry
                    Fmeca::create($data); // Save to database
                }
                $pesan = "🛠️ Informasi Critical Part \n";
                $pesan .= "👤 *User:* {$userName}\n";
                $pesan .= "📄 *Project:* {$projectTitle}\n";
                $pesan .=  "📊 Downloadfile_{$fmecaFile->id}\n\n"
                    . "Silakan cek data FMECA baru di sistem.\n\n"
                    . route('fmeca.show', ['fmeca_identity_id' => $fmecaidentity->id]) . "\n\n"
                    . "Terima kasih!";

                // TelegramService::sendTeleMessage(['6281515814752'], $pesan);
                TelegramService::ujisendunit('Quality Engineering', $pesan);
                $pesaninfo = "🛠️ Informasi Critical Part Telah terkirim ke Quality Engineering!* 📢\n"
                    . "👤 *User:* {$userName}\n"
                    . "📄 *Project:* {$projectTitle}\n"
                    . "📊 Downloadfile_{$fmecaFile->id}\n\n"
                    . "Silakan cek data FMECA baru di sistem.\n\n"
                    . route('fmeca.show', ['fmeca_identity_id' => $fmecaidentity->id]) . "\n\n"
                    . "Terima kasih!";
                // TelegramService::sendTeleMessage(['6281515814752'], $pesaninfo);
                TelegramService::ujisendunit('RAMS', $pesaninfo);

                $revisiData = array_merge($revisiData, $sheetRevisiData);
            }

            // Commit the transaction if all operations are successful
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to process the Excel file: ' . $e->getMessage()], 500);
        }

        // Return the response
        return response()->json([
            'message' => 'FMECA data processed and saved successfully',
            'data' => $revisiData
        ]);
    }


    private function validateExcelStructure($importedData)
    {
        foreach ($importedData as $sheetname => $sheetData) {
            if (empty($sheetData[0][1])) {
                throw new \Exception("Project name in cell B1 of sheet '$sheetname' is missing.");
            }
            if (count($sheetData) <= 5) {
                throw new \Exception("Sheet '$sheetname' does not contain enough rows for data processing.");
            }
        }
    }
    public function progressreportexported($importedData, $sheetname)
    {
        $revisiData = [];

        $project_type = trim($importedData[0][1] ?? ""); // Get project name from B1
        $project_type_model = ProjectType::where('title', $project_type)->first();

        if (!$project_type_model) {
            throw new \Exception("Project type '$project_type' not found in database for sheet '$sheetname'.");
        }
        $project_type_id = $project_type_model->id;

        foreach ($importedData as $key => $row) {
            $safety = trim($row[15] ?? ""); // Column P
            $reliability = trim($row[18] ?? ""); // Column S
            // Determine whether safety or reliability is filled
            $issafetyorisreliability = !empty($safety) ? 'safety' : (!empty($reliability) ? 'reliability' : '');
            if ($issafetyorisreliability === '') {
                continue; // Skip if both are empty
            }

            $notifvalue = $issafetyorisreliability === 'safety' ? $safety : $reliability;

            // Skip this row if the value in column P (safety) or S (reliability) is empty
            if (!empty($notifvalue)) {
                $notifvalue = strtolower(trim($notifvalue));
                if ($notifvalue !== 'undesirable' && $notifvalue !== 'intolerable') {
                    continue;
                }
                if (!empty($project_type_id)) {
                    $revisiData[] = [
                        'project_type_id' => $project_type_id,
                        'notifvalue' => $notifvalue,
                        'issafetyorisreliability' => $issafetyorisreliability,
                        'subsystemname' => $sheetname
                    ];
                }
            }
        }
        return $revisiData;
    }

    private function validateExcel(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls',
        ]);
    }

    private function importExcel($file)
    {
        $data = [];
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getRealPath());
        foreach ($spreadsheet->getSheetNames() as $sheetName) {
            $sheet = $spreadsheet->getSheetByName($sheetName);
            $sheetData = [];
            foreach ($sheet->getRowIterator() as $row) {
                $rowData = [];
                foreach ($row->getCellIterator() as $cell) {
                    // Get the calculated value of the cell
                    $rowData[] = $cell->getCalculatedValue();
                }
                $sheetData[] = $rowData;
            }
            $data[$sheetName] = $sheetData;
        }
        return $data;
    }
    public function download($project)
    {
        // Fetch FMECA data for the given project
        $fmecaData = Fmeca::where('project_type_id', $project)->with('projectType')->get();
        $projectTitle = ProjectType::findOrFail($project)->title;

        // Create a new spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('FMECA Report');

        // Set headers
        $headers = ['No', 'Project', 'Subsystem', 'Issue', 'Type', 'Spreadsheet Link'];
        $sheet->fromArray($headers, null, 'A1');

        // Populate data
        $rowNumber = 2;
        foreach ($fmecaData as $index => $item) {
            $sheet->fromArray([
                $index + 1,
                $item->projectType->title,
                $item->subsystemname,
                $item->notifvalue,
                $item->issafetyorisreliability,
                "Downloadfile_{$item->id}",
            ], null, "A{$rowNumber}");
            $rowNumber++;
        }

        // Auto-size columns
        foreach (range('A', 'F') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        // Set headers for download
        $filename = "FMECA_Report_{$projectTitle}_" . date('Ymd_His') . ".xlsx";
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"{$filename}\"");
        header('Cache-Control: max-age=0');

        // Write to output
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    public function destroy($id)
    {
        try {
            $fmeca = Fmeca::findOrFail($id);
            $fmeca->delete();
            return response()->json(['message' => 'FMECA data deleted successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to delete FMECA data: ' . $e->getMessage()], 500);
        }
    }

    public function index()
    {
        $allproject = ProjectType::all(); // Ensure ProjectType is loaded if needed
        return view('fmeca.index', compact('allproject'));
    }
    // Add this method to support the index.blade.php data fetching
    public function data(Request $request)
    {
        $projectId = $request->input('project_id');

        if (!$projectId) {
            return response()->json([
                'error' => 'Project ID is required',
                'data' => [],
                'total_identities' => 0,
            ], 400);
        }

        try {
            $identities = FmecaIdentity::where('project_type_id', $projectId)
                ->with(['projectType', 'user', 'files'])
                ->get()
                ->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'project_title' => optional($item->projectType)->title ?? 'N/A',
                        'user' => optional($item->user)->name ?? 'N/A',
                        'created_at' => $item->created_at,
                        'files' => $item->files->map(function ($file) {
                            return [
                                'filename' => $file->filename,
                                'link' => $file->link,
                            ];
                        })->toArray(),
                    ];
                });

            return response()->json([
                'data' => $identities,
                'total_identities' => $identities->count(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'An error occurred while fetching FMECA Identity data',
                'data' => [],
                'total_identities' => 0,
            ], 500);
        }
    }
}
