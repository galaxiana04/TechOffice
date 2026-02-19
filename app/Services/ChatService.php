<?php
// app/Services/ChatService.php
namespace App\Services;

use Illuminate\Support\Facades\Http;

class ChatService
{
    protected string $baseUrl = "http://147.93.103.168:5678/webhook";

    public function sendMessage(string $sessionId, string $message): string
    {
        $response = Http::timeout(180)->get("{$this->baseUrl}/1d0adad3-07e3-4850-bc0a-ca472ee9691d", [
            'chatInput' => $message,
            'sessionId' => $sessionId,
        ]);

        if ($response->failed()) {
            return "⚠️ Error: " . $response->status();
        }

        return $response->body();
    }
}
