<?php

namespace App\Http\Controllers;

use App\Models\ChatSessionKatalogKomat;
use App\Models\ChatMessageKatalogKomat;
use App\Services\ChatService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class KatalogKomatChatController extends Controller
{
    public function index()
    {
        $sessions = ChatSessionKatalogKomat::all();
        $activeSession = session('chat_session_id')
            ? ChatSessionKatalogKomat::where('session_id', session('chat_session_id'))->first()
            : null;

        $messages = $activeSession ? $activeSession->messages : collect();

        if (!$activeSession && session('chat_session_id')) {
            // Clear invalid session ID
            session()->forget('chat_session_id');
            return redirect()->route('chatyourkomat.index')->with('error', 'Invalid session selected.');
        }

        return view('chatyourkomat.index', compact('sessions', 'activeSession', 'messages'));
    }

    public function newSession()
    {
        $session = ChatSessionKatalogKomat::create([
            'session_id' => (string) Str::uuid(),
            'name' => 'Session ' . now()->format('Y-m-d H:i:s')
        ]);

        session(['chat_session_id' => $session->session_id]);

        return redirect()->route('chat.index');
    }

    public function switchSession($sessionId)
    {
        session(['chat_session_id' => $sessionId]);
        return redirect()->route('chat.index');
    }

    public function send(Request $request, ChatService $chatService)
    {
        $request->validate([
            'message' => 'required|string',
        ]);

        $sessionId = session('chat_session_id');

        if (!$sessionId) {
            return response()->json(['success' => false, 'message' => 'Session tidak ditemukan']);
        }

        $session = ChatSessionKatalogKomat::where('session_id', $sessionId)->firstOrFail();

        // Simpan pesan user
        ChatMessageKatalogKomat::create([
            'chat_session_id' => $session->id,
            'sender' => 'user',
            'message' => $request->message,
        ]);

        // Kirim ke service
        $rawReply = $chatService->sendMessage($sessionId, $request->message);

        $decoded = json_decode($rawReply, true);
        $botReply = $decoded[0]['output'] ?? $rawReply;

        // Simpan balasan bot
        ChatMessageKatalogKomat::create([
            'chat_session_id' => $session->id,
            'sender' => 'bot',
            'message' => $botReply,
        ]);

        return response()->json([
            'success' => true,
            'botReply' => $botReply
        ]);
    }
}
