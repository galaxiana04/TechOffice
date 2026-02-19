<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class GpsController extends Controller
{
    public function index()
    {
        return view('gps.index');
    }

    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv,txt'
        ]);

        $data = [];
        if (($handle = fopen($request->file('file')->getRealPath(), 'r')) !== FALSE) {
            $header = fgetcsv($handle); // skip header
            while (($row = fgetcsv($handle)) !== FALSE) {
                $data[] = [
                    'datetime' => $row[0],
                    'latitude' => (float)$row[1],
                    'longitude' => (float)$row[2],
                    'speed_kmh' => (float)$row[3],
                    'altitude_m' => (float)$row[4],
                ];
            }
            fclose($handle);
        }

        return view('gps.index', compact('data'));
    }
}
