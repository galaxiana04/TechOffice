<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\NewMemo;
use App\Models\Unit;
use App\Models\User;
use Carbon\Carbon;

class MonitoringUnitController extends Controller
{
    public function index()
    {
        $monitoringData = NewMemo::getDashboardSpeedResume();
        
        $unitMembers = $this->getUnitMembers();
        
        
        return view('newmemo.monitoring.unit', compact('monitoringData', 'unitMembers'));
    }
    
    private function getUnitMembers()
    {
        $units = Unit::where('is_technology_division', true)->get();
        $members = [];
        
        foreach ($units as $unit) {

            $count = User::where('unit_id', $unit->id)->count();
            
            $members[$unit->name] = $count;
        }
        
        return $members;
    }
    
    public function getUnitDetail(Request $request)
    {
        $unitName = $request->unit;
        $rangeKey = $request->range;
        
        $days = 90;
        if ($rangeKey == '7_days') $days = 7;
        if ($rangeKey == '30_days') $days = 30;
        
        $startDate = Carbon::now()->subDays($days);
        
        $memos = NewMemo::with(['feedbacks', 'timelines'])
            ->where('created_at', '>=', $startDate)
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Hitung jumlah anggota unit
        $unit = Unit::where('name', $unitName)->first();
        $memberCount = 0;
        
        if ($unit) {
            $memberCount = User::where('unit_id', $unit->id)->count();
        }
        
        $detailList = [];
        
        foreach ($memos as $memo) {
            $leadTimes = $memo->leadtimeunit();
            
            if (isset($leadTimes[$unitName]) && is_numeric($leadTimes[$unitName])) {
                $val = floatval($leadTimes[$unitName]);
                
                $badge = 'danger';
                if ($val < 24) $badge = 'success';
                elseif ($val < 72) $badge = 'warning';
                
                $detailList[] = [
                    'documentnumber' => $memo->documentnumber,
                    'documentname' => $memo->documentname,
                    'created_at' => Carbon::parse($memo->created_at)->format('d M Y H:i'),
                    'leadtime' => NewMemo::formatDuration($val),
                    'leadtime_hours' => $val,
                    'badge' => $badge,
                    'member_count' => $memberCount
                ];
            }
        }
        
        return response()->json($detailList);
    }
    
}