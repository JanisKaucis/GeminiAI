<?php

namespace App\Services;

use App\Models\conversation;
use Gemini\Data\Content;
use Gemini\Data\Part;
use Gemini\Enums\Role;
use Gemini\Laravel\Facades\Gemini;

class GeminiService
{
    public function sendMessage($question, $windowId): string
    {
        $conversation = Conversation::firstOrCreate([
            'user_id' => auth()->id(),
            'window_id' => $windowId,
        ], [
            'title' => $question,
        ]);

        return $this->continueConversation($conversation, $question);
    }

    public function continueConversation(Conversation $conversation, string $userMessage): string
    {
        $conversation->messages()->create([
            'role' => 'user',
            'message' => $userMessage,
        ]);

        $history = $conversation->messages()
            ->latest()
            ->take(101)
            ->get(['role', 'message', 'created_at'])
            ->sortBy('created_at')
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

        $chat = Gemini::chat(model: 'gemini-2.0-flash')
            ->startChat($history);
        $response = $chat->sendMessage($userMessage);

        $conversation->messages()->create([
            'role' => 'model',
            'message' => $response->text(),
        ]);

        return $response->text();
    }

    public function getConversations()
    {
        return Conversation::where('user_id', auth()->id())->limit(10)->orderBy('created_at', 'desc')->get();
    }
}
