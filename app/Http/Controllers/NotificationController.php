<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\Unit;
use App\Models\User;
use App\Models\Wagroupnumber;
use App\Services\TelegramService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NotificationController extends Controller
{

    public function showByDivisi($namaDivisi)
    {
        $namaDivisi = auth()->user()->rule;

        $unit = Unit::where('name', $namaDivisi)->first();

        if (!$unit) {
            return view('showinformation.info', ['message' => 'Unit tidak ditemukan']);
        }

        // Eager load the 'memo' relationship in the Notification model
        $notifs = Notification::where('idunit', $unit->id)
            ->with(['memo.projectType', 'notifmessage', 'memosekdiv.projectType', 'ramsdocument']) // Eager loading the 'memo' relationship
            ->orderBy('created_at', 'desc')
            ->get();

        if ($notifs->isEmpty()) {
            return view('showinformation.info', ['message' => 'Tidak ada data untuk unit ini']);
        }

        $unreadCount = 0; // Initialize unread count

        foreach ($notifs as $notif) {
            if ($notif->notifmessage_type == 'App\Models\NewMemo' && optional($notif->memo)->documentstatus == 'Terbuka') {
                $unreadCount++;
            } elseif ($notif->notifmessage_type == 'App\Models\MemoSekdiv' && optional($notif->memosekdiv)->documentstatus == 'open') {
                $unreadCount++;
            }
        }

        return view('notification.mailbox', compact('namaDivisi', 'notifs', 'unreadCount'));
    }


    public function sendwa(Request $request)
    {

        $pesan = $request->input('pesan');
        $senderName = $request->input('sender_name'); // Ambil nama pengirim dari request
        $kindreceiver = $request->input('kindreceiver');

        if ($kindreceiver == 'group') {
            try {
                $listunit = $request->input('listunit');
                foreach ($listunit as $unit) {
                    TelegramService::ujisendunit($unit, "$pesan\n\nDikirim oleh: $senderName"); // Handle group message sending
                    // $unit disini diisi name dari model unit misal Quality Engineering
                }

                // Return a JSON response indicating success
                return response()->json([
                    'status' => 'success',
                    'message' => 'Pesan berhasil dikirim ke grup.'
                ], 200);
            } catch (\Exception $e) {
                // Handle any exceptions that may occur and return a JSON error response
                return response()->json([
                    'status' => 'error',
                    'message' => 'Terjadi kesalahan saat mengirim pesan ke grup: ' . $e->getMessage()
                ], 500);
            }
        } else {
            try {
                $listnohp = $request->input('phonenumbers');
                // Send WhatsApp message with sender name
                TelegramService::sendTeleMessage($listnohp, "$pesan\n\nDikirim oleh: $senderName");

                // Return a JSON response indicating success
                return response()->json([
                    'status' => 'success',
                    'message' => 'Pesan berhasil dikirim ke nomor individu.'
                ], 200);
            } catch (\Exception $e) {
                // Handle any exceptions that may occur and return a JSON error response
                return response()->json([
                    'status' => 'error',
                    'message' => 'Terjadi kesalahan saat mengirim pesan ke nomor individu: ' . $e->getMessage()
                ], 500);
            }
        }
    }


    public function viewsendwa()
    {
        // Ambil nama pengguna yang diautentikasi
        $senderName = auth()->user()->name ?? 'Anonim';

        // Ambil data user (name dan waphonenumber) dari database, hanya yang memiliki waphonenumber
        $userphonebook = User::whereNotNull('waphonenumber')
            ->select('name', 'waphonenumber')
            ->get();
        $wagrouplist = Unit::where('name', 'not like', '%Manager%')->get();

        // Kirimkan nama pengirim dan data user ke view
        return view('notification.sendwa', compact('senderName', 'userphonebook', 'wagrouplist'));
    }
}
