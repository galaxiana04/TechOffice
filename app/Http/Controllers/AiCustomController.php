<?php

namespace App\Http\Controllers;

use App\Models\AiCustom;
use App\Models\AiCustomSpeciality;
use Illuminate\Http\Request;

class AiCustomController extends Controller
{
    public function index()
    {
        $data = AiCustom::with('speciality')->get();
        $specialities = AiCustomSpeciality::all();
        return view('aicustom.index', compact('data', 'specialities'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'keyword' => 'required|unique:ai_customs,keyword',
            'description' => 'required',
            'output' => 'required',
            'aicustomspeciality_id' => 'nullable|exists:ai_custom_specialities,id'
        ]);

        $data = AiCustom::create($request->all());

        return response()->json(['success' => 'Data berhasil ditambahkan!', 'data' => $data]);
    }

    public function update(Request $request, AiCustom $aicustom)
    {
        $request->validate([
            'keyword' => 'required|unique:ai_customs,keyword,' . $aicustom->id,
            'description' => 'required',
            'output' => 'required',
            'aicustomspeciality_id' => 'nullable|exists:ai_custom_specialities,id'
        ]);

        $aicustom->update($request->all());

        return response()->json(['success' => 'Data berhasil diperbarui!', 'data' => $aicustom]);
    }

    public function destroy(AiCustom $aicustom)
    {
        $aicustom->delete();
        return response()->json(['success' => 'Data berhasil dihapus!']);
    }

    public function show($keyword)
    {
        $data = AiCustom::with('speciality')->where('keyword', $keyword)->firstOrFail();

        if (!$data) {
            return response()->json(['error' => 'Data tidak ditemukan'], 404);
        }

        return response()->json($data);
    }

}
