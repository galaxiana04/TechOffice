<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RollingstockSpec;
use App\Models\RollingstockType;
use App\Models\RollingstockDesignation;
use App\Models\ProjectType;
use App\Models\CollectFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class RollingstockSpecController extends Controller
{
    public function index()
    {
        $rollingstockSpecs = RollingstockSpec::with(['rollingstockType', 'rollingstockDesignation', 'projectType'])->get();
        $rollingstockTypes = RollingstockType::all();
        $rollingstockDesignations = RollingstockDesignation::all();
        $projectTypes = ProjectType::all();

        return view('rollingstock.index', compact('rollingstockSpecs', 'rollingstockTypes', 'rollingstockDesignations', 'projectTypes'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'climate' => 'required|string|max:255',
            'average_temperature' => 'nullable|numeric',
            'lowest_temperature' => 'nullable|numeric',
            'highest_temperature' => 'nullable|numeric',
            'highest_operating_altitude' => 'nullable|numeric',
            'rollingstock_type_id' => 'required|exists:rollingstock_types,id',
            'rollingstock_designation_id' => 'required|exists:rollingstock_designations,id',
            'axle_load_of_rollingstock' => 'nullable|numeric',
            'load_capacity' => 'nullable',
            'track_gauge' => 'nullable|numeric',
            'max_height_of_rollingstock' => 'nullable|numeric',
            'max_width_of_rollingstock' => 'nullable|numeric',
            'max_length_of_rollingstock_include_coupler' => 'nullable|numeric',
            'coupler_height' => 'nullable|numeric',
            'coupler_type' => 'nullable|string|max:255',
            'proyek_type_id' => 'required|exists:project_types,id',
            'minimum_horizontal_curve_radius' => 'required',
            'maximum_sustained_gradient_at_main_line' => 'required',
            'maximum_sustained_gradient_at_depot' => 'required',
            'distance_between_bogie_centers' => 'required',
            'distance_between_axle' => 'required',
            'wheel_diameter' => 'required',
            'floor_height_from_top_of_rail' => 'required',
            'maximum_design_speed' => 'required',
            'maximum_operation_speed' => 'required',
            'acceleration_rate' => 'nullable',
            'minimum_deceleration_rate' => 'nullable',
            'minimum_emergency_deceleration' => 'nullable',
            'bogie_type' => 'required',
            'brake_system' => 'required',
            'propulsion_system' => 'required',
            'suspension_system' => 'required',
            'carbody_material' => 'required',
            'air_conditioning_system' => 'required',
            'other_requirements' => 'required',



        ]);

        DB::beginTransaction(); // Mulai transaksi\
        try {
            $rollingstockSpec = RollingstockSpec::create([
                'climate' => $request->climate,
                'average_temperature' => $request->average_temperature,
                'lowest_temperature' => $request->lowest_temperature,
                'highest_temperature' => $request->highest_temperature,
                'highest_operating_altitude' => $request->highest_operating_altitude,
                'rollingstock_type_id' => $request->rollingstock_type_id,
                'rollingstock_designation_id' => $request->rollingstock_designation_id,
                'axle_load_of_rollingstock' => $request->axle_load_of_rollingstock,
                'load_capacity' => $request->load_capacity,
                'track_gauge' => $request->track_gauge,
                'max_height_of_rollingstock' => $request->max_height_of_rollingstock,
                'max_width_of_rollingstock' => $request->max_width_of_rollingstock,
                'max_length_of_rollingstock_include_coupler' => $request->max_length_of_rollingstock_include_coupler,
                'coupler_height' => $request->coupler_height,
                'coupler_type' => $request->coupler_type,
                'proyek_type_id' => $request->proyek_type_id,
                'distance_between_bogie_centers' => $request->distance_between_bogie_centers,
                'distance_between_axle' => $request->distance_between_axle,
                'wheel_diameter' => $request->wheel_diameter,
                'floor_height_from_top_of_rail' => $request->floor_height_from_top_of_rail,
                'maximum_design_speed' => $request->maximum_design_speed,
                'maximum_operation_speed' => $request->maximum_operation_speed,
                'acceleration_rate' => $request->acceleration_rate,
                'minimum_deceleration_rate' => $request->minimum_deceleration_rate,
                'minimum_emergency_deceleration' => $request->minimum_emergency_deceleration,
                'bogie_type' => $request->bogie_type,
                'brake_system' => $request->brake_system,
                'propulsion_system' => $request->propulsion_system,
                'suspension_system' => $request->suspension_system,
                'carbody_material' => $request->carbody_material,
                'air_conditioning_system' => $request->air_conditioning_system,
                'other_requirements' => $request->other_requirements,
                'minimum_horizontal_curve_radius' => $request->minimum_horizontal_curve_radius,
                'maximum_sustained_gradient_at_main_line' => $request->maximum_sustained_gradient_at_main_line, // Tambahkan ini
                'maximum_sustained_gradient_at_depot' => $request->maximum_sustained_gradient_at_depot, // Tambahkan ini
            ]);

            DB::commit(); // Commit transaksi jika tidak ada error
            return response()->json([
                'success' => true,
                'message' => 'Data berhasil ditambahkan!',
                'data' => $rollingstockSpec
            ]);
        } catch (\Exception $e) {
            DB::rollBack(); // Rollback transaksi jika terjadi error
            return response()->json(['error' => 'Gagal menyimpan data: ' . $e->getMessage()], 422);
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'climate' => 'required',
            'rollingstock_type_id' => 'required',
            'rollingstock_designation_id' => 'required',
            'proyek_type_id' => 'required',
            'file.*' => 'file|mimes:jpg,jpeg,png', // Validasi file
        ]);
        DB::beginTransaction(); // <-- Mulai transaksi
        try {


            $rollingstockSpec = RollingstockSpec::findOrFail($id);
            $rollingstockSpec->update($request->all());
            // Update user information
            $user = auth()->user();
            $userName = $user->name;
            // Handle file uploads
            if ($request->hasFile('file')) {
                foreach ($request->file('file') as $uploadedFile) {
                    $filename = $uploadedFile->getClientOriginalName();
                    $fileFormat = $uploadedFile->getClientOriginalExtension();
                    $filenameWithoutExtension = pathinfo($filename, PATHINFO_FILENAME);
                    $filenameWithUserAndFormat = $filenameWithoutExtension . '_' . $userName . '.' . $fileFormat;
                    $filename = $filenameWithUserAndFormat;

                    $count = 0;
                    $newFilename = $filename;
                    while (CollectFile::where('filename', $newFilename)->exists()) {
                        $count++;
                        $newFilename = pathinfo($filename, PATHINFO_FILENAME) . '_' . $count . '.' . pathinfo($filename, PATHINFO_EXTENSION);
                    }

                    $path = $uploadedFile->storeAs('public/uploads', $newFilename);

                    $newmemoFile = new CollectFile();
                    $newmemoFile->filename = $newFilename;
                    $newmemoFile->link = str_replace('public/', '', $path);
                    $newmemoFile->collectable_id = $rollingstockSpec->id;
                    $newmemoFile->collectable_type = RollingstockSpec::class;
                    $newmemoFile->save();
                }
            }

            if ($request->filecount > 0) {
                for ($i = 0; $i < $request->filecount; $i++) {
                    $newmemoFile = new CollectFile();
                    $newmemoFile->filename = "filekosong";
                    $newmemoFile->link = '';
                    $newmemoFile->collectable_id = $rollingstockSpec->id;
                    $newmemoFile->collectable_type = RollingstockSpec::class;
                    $newmemoFile->save();
                }
            }

            DB::commit(); // <-- Commit transaksi jika tidak ada error
            return response()->json(['success' => 'Data berhasil diperbarui!', 'data' => $rollingstockSpec]);
        } catch (\Exception $e) {
            DB::rollBack(); // <-- Rollback transaksi jika terjadi error
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $rollingstockSpec = RollingstockSpec::findOrFail($id);
            $collectFiles = CollectFile::where('collectable_id', $rollingstockSpec->id)
                ->where('collectable_type', RollingstockSpec::class)
                ->get();

            foreach ($collectFiles as $file) {
                if (file_exists(storage_path('app/' . $file->link))) {
                    unlink(storage_path('app/' . $file->link));
                }
                $file->delete();
            }

            $rollingstockSpec->delete();
            DB::commit();

            return response()->json(['success' => 'Data berhasil dihapus!']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Gagal menghapus data: ' . $e->getMessage()], 422);
        }


        return response()->json(['success' => 'Data berhasil dihapus!']);
    }

    public function getRollingstock($id)
    {
        $rollingstock = RollingstockSpec::with('files')->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $rollingstock
        ]);
    }

    public function deleteFile($fileId)
    {
        DB::beginTransaction();
        try {
            $file = CollectFile::findOrFail($fileId);
            // Delete file from storage
            Storage::delete('public/' . $file->link); // Adjust path based on your storage setup
            $file->delete();
            DB::commit();
            return response()->json(['message' => 'Gambar berhasil dihapus!']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }
}
