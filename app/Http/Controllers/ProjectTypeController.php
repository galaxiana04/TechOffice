<?php

namespace App\Http\Controllers;

use App\Models\ProjectType;
use Illuminate\Http\Request;

class ProjectTypeController extends Controller
{

    public function jsondata()
    {
        $projectTypes = ProjectType::all();
        return response()->json($projectTypes, 200);
    }



    public function index()
    {
        $projectTypes = ProjectType::all();
        return view('project_types.index', compact('projectTypes'));
    }

    public function create()
    {
        return view('project_types.create');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
        ]);

        $projectType = ProjectType::create([
            'title' => $request->title,
            'project_code' => $request->project_code ?? "",
            'vault_link' => $request->vault_link ?? "",

        ]);

        return response()->json([
            'success' => true,
            'message' => 'Project Type created successfully.',
            'data' => $projectType
        ]);
    }





    public function update(Request $request, $id)
    {
        $projectType = ProjectType::findOrFail($id);
        $projectType->update([
            'title' => $request->title,
            'project_code' => $request->project_code,
            'vault_link' => $request->vault_link
        ]);

        return response()->json(['success' => true]);
    }

    public function destroy($id)
    {
        ProjectType::findOrFail($id)->delete();
        return response()->json(['success' => true]);
    }



    public function data()
    {
        $data = ProjectType::all();

        return datatables()->of($data)->addIndexColumn()->addColumn('action', function ($data) {
            return '
            <div class="btn-group">
            <button onclick="editForm(`' . route('project_types.update', $data->id) . '`)" class="btn btn-link text-primary"><i class="fas fa-pencil-alt"></i></button>
            <button onclick="deleteData(`' . route('project_types.destroy', $data->id) . '`)"class="btn btn-link text-danger"><i class="fas fa-trash-alt"></i></button>
            </div>
            ';
        })
            ->rawColumns(['action'])
            ->make(true);
    }
}