<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use App\Models\Tack;
use App\Models\SubTack;
use App\Models\SubTackMember;
use App\Models\TackPhase;
use App\Models\Newprogressreport;
use App\Models\ProjectType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Maatwebsite\Excel\Facades\Excel;
use Exception;
use App\Imports\RawprogressreportsImport;

class TackController extends Controller
{
    public function getProjectData($id)
    {
        // Ambil semua Tack beserta SubTack dan SubTackMember
        $tacks = Tack::where("proyek_type_id", $id)->with(['projectType', 'subtacks.subtackMembers.newprogressreports', 'tackPhase'])->get();
        return response()->json($tacks);
    }

    public function index()
    {
        $projects = ProjectType::all();
        return view('tack.tree', compact('projects'));
    }

    public function getProjects()
    {
        return response()->json(ProjectType::all());
    }

    public function upload()
    {
        return view('tack.upload');
    }

    public function importExcelsistem(Request $request)
    {
        $jenisupload = $request->jenisupload;

        if ($jenisupload == "formatprogress") {
            $hasil = $this->formatprogress($request);
        }
        return $hasil;
    }



    public function progressreportexported($importedData)
    {
        $revisiData = [];
        $existingProjects = ProjectType::pluck('id', 'title');
        $existingProgressReports = Newprogressreport::pluck('id', 'nodokumen');
        $existingTackPhase = TackPhase::pluck('id', 'name');


        foreach ($importedData as $key => $row) {

            $proyek_type_id = $existingProjects[trim($row[1] ?? "")] ?? null;
            $newprogressreport_id = $existingProgressReports[trim($row[7] ?? "")] ?? null;
            $tack_phase_id = $existingTackPhase[trim($row[3] ?? "")] ?? null;


            // Jika proyek_type_id tidak ditemukan, lewati baris ini
            if (is_null($proyek_type_id) || is_null($newprogressreport_id)) {
                continue;
            }

            // Konversi ke integer jika berupa string angka
            $tack = is_numeric($row[2] ?? null) ? (int) $row[2] : null;



            $subtack = is_numeric($row[4] ?? null) ? (int) $row[4] : null;
            $subtackdocumentnumber = trim($row[5] ?? "");
            $activity = trim($row[6] ?? "");



            // Filter: Jika ada data yang null, "", atau "0", lewati baris ini
            if (
                is_null($tack) || $tack === 0 ||
                is_null($subtack) || $subtack === 0 ||
                $activity === "" || $activity === "0" || is_null($activity) ||
                $newprogressreport_id === "" || $newprogressreport_id === "0" || is_null($newprogressreport_id)
            ) {
                continue;
            }

            $revisiData[] = [
                'proyek_type_id' => $proyek_type_id,
                'newprogressreport_id' => $newprogressreport_id,
                'tack' => $tack,
                'subtack' => $subtack,
                'subtackdocumentnumber' => $subtackdocumentnumber,
                'activity' => $activity,
                'tack_phase_id' => $tack_phase_id,
            ];
        }

        return $revisiData;
    }




    public function formatprogress(Request $request)
    {
        $validatedData = $request->validate([
            'file' => 'required|file|mimes:xlsx,xls',
        ]);

        $file = $request->file('file');

        try {
            $import = new RawprogressreportsImport();
            $revisiData = Excel::toCollection($import, $file)->first();

            if (empty($revisiData)) {
                return response()->json(['error' => 'No data found in the Excel file.'], 400);
            }

            // Ambil semua ProjectType, Tack, SubTack, SubTackMember, dan Newprogressreport sekaligus

            $existingTacks = Tack::pluck('id', 'number');
            $existingSubTacks = SubTack::pluck('id', 'number');
            $existingActivities = SubTackMember::pluck('id', 'name');



            $newRelations = [];

            $processedData = $this->progressreportexported($revisiData);



            foreach ($processedData as $item) {
                $proyek_type_id = trim($item['proyek_type_id']);
                $tackname = trim($item['tack']);
                $tack_id = $existingTacks[$tackname] ?? Tack::create([
                    'number' => $tackname,
                    'proyek_type_id' => $proyek_type_id,
                    'tack_phase_id' => $item['tack_phase_id']
                ])->id;
                $existingTacks[$tackname] = $tack_id;

                $subtackname = trim($item['subtack']);


                $subtackdocumentnumber = trim($item['subtackdocumentnumber']);



                $subtack_id = $existingSubTacks[$subtackname] ?? SubTack::create(['number' => $subtackname, 'tack_id' => $tack_id, 'documentnumber' => $subtackdocumentnumber])->id;
                $existingSubTacks[$subtackname] = $subtack_id;

                $activityname = trim($item['activity']);
                if (!isset($existingActivities[$activityname])) {
                    $activity = SubTackMember::create([
                        'name' => $activityname,
                        'subtack_id' => $subtack_id
                    ]);
                    $existingActivities[$activityname] = $activity->id;
                }


                $newRelations[] = [
                    'subtack_member_id' => $existingActivities[$activityname],
                    'newprogressreport_id' => $item['newprogressreport_id'],
                ];
            }



            // Insert batch ke tabel pivot
            if (!empty($newRelations)) {
                DB::table('newprogressreport_subtack_member')->insert($newRelations);
            }

            return response()->json(['message' => 'Data Excel successfully imported'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }



}
