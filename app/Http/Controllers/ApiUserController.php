<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class ApiUserController extends Controller
{
    public function getUsers(Request $request)
    {
        // API KEY statis contoh
        $validKey = 'managernova54321';

        // Ambil API KEY dari header
        $apiKey = $request->header('X-API-KEY');

        // Validasi API KEY
        if ($apiKey !== $validKey) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid API Key'
            ], 401);
        }

        // Jika API key benar → ambil hanya nama
        $users = User::select('name')->get();

        return response()->json([
            'status' => 'success',
            'data' => $users
        ]);
    }
    public function getWaPhones(Request $request)
    {
        $validKey = 'managernova54321';
        $apiKey = $request->header('X-API-KEY');

        if ($apiKey !== $validKey) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid API Key'
            ], 401);
        }

        // Ambil username dari query parameter
        $username = $request->input('username');

        if (!$username) {
            return response()->json([
                'status' => 'error',
                'message' => 'Username is required'
            ], 400);
        }

        // Ambil wa_phone_number berdasarkan username
        $user = User::select('wa_phone_number')
            ->where('username', $username)
            ->first();

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'User not found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $user
        ]);
    }
}
