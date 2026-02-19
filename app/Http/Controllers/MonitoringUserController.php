<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MonitoringUserController extends Controller
{
    public function index()
    {
        $users = User::with(['unit'])
            ->withCount([
                'progressHistories as total_drafter',
                'progressHistoriesAsChecker as total_checker',
            ])
            ->get()
            ->map(function ($u) {
                $u->total_work = $u->total_drafter + $u->total_checker;
                return $u;
            })
            ->sortByDesc('total_work');

        $groupedUsers = $users->groupBy('rule');

        return view('newreports.monitoring_user', compact('groupedUsers', 'users'));
    }

    public function show($id)
    {
        $user = User::with(['unit'])->findOrFail($id);

        $initial = $user->initial; // kolom inisial di tabel users

        $histories = \App\Models\Newprogressreporthistory::where('drafter', $initial)
            ->orWhere('checker', $initial)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($h) use ($initial) {
                return [
                    'nodokumen' => $h->nodokumen,
                    'namadokumen' => $h->namadokumen,
                    'status' => $h->status,
                    'role' => $h->drafter == $initial ? 'drafter' : 'checker',
                    'realisasidate' => $h->realisasidate,
                ];
            });

        return response()->json([
            'user' => $user,
            'total_work' => $histories->count(),
            'histories' => $histories,
        ]);
    }
}