<?php

namespace App\Services;

use App\Models\conversation;
use Gemini\Data\Content;
use Gemini\Data\Part;
use Gemini\Enums\Role;
use Gemini\Laravel\Facades\Gemini;
use Illuminate\Support\Facades\Log;

class GeminiService
{
    public function sendMessage($question, $windowId)
    {
        $conversation = Conversation::firstOrCreate([
            'user_id' => auth()->id(),
            'window_id' => $windowId,
        ], [
            'title' => $question,
        ]);

        return $this->continueConversation($conversation, $question);
    }

    public function continueConversation(Conversation $conversation, string $userMessage)
    {
        $conversation->messages()->create([
            'role' => 'user',
            'message' => $userMessage,
        ]);

        $history = $conversation->messages()
            ->orderBy('created_at')
            ->get(['role', 'message'])
            ->map(function ($m) {
                $role = strtolower($m->role) === 'model'
                    ? Role::MODEL
                    : Role::USER;

                return new Content(
                    parts: [new Part(text: $m->message)],
                    role: $role
                );
            })
            ->values()
            ->all();

        $chat = Gemini::chat(model: 'gemini-2.0-flashs')
            ->startChat($history);
        $response = $chat->sendMessage($userMessage);

        $conversation->messages()->create([
            'role' => 'model',
            'message' => $response->text(),
        ]);

        return $response->text();
    }
}
