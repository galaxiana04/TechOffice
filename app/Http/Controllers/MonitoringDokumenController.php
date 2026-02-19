<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Newreport;
use App\Models\ProjectType;

class MonitoringDokumenController extends Controller
{
    public function index(Request $request)
    {
        
        $projects = ProjectType::all();
        
        $selectedProject = $request->input('project', 'All');

        $stats = Newreport::getDocumentDashboardStats($selectedProject);

        return view('newreports.monitoring_dokumen', [
            'projects' => $projects,
            'selectedProject' => $selectedProject,
            'stats' => $stats
        ]);
    }
}