<?php

namespace App\Services;

use App\Models\GeminiMessage;
use Gemini\Data\Content;
use Gemini\Laravel\Facades\Gemini;
use Illuminate\Support\Facades\Log;

class GeminiService
{
    public function getAnswerFromGeminiAPI($question)
    {
        try {
            $userId = auth()->id();
            $history = GeminiMessage::where('user_id', $userId)
                ->latest()->take(20)
                ->get(['role', 'content'])->toArray();

            $chat = Gemini::chat(model: 'gemini-2.0-flash')
                ->startChat($history);
            $result = $chat->sendMessage($question);
            $response = $result->text();
            Log::debug($response);
            GeminiMessage::create(['user_id' => $userId, 'content' => $response, 'role' => 'user']);

            return $response;

        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return 'Something went wrong';
        }
    }

    public function streamAnswerFromGeminiAPI($question): void
    {
        $result = Gemini::generativeModel(model: 'gemini-2.0-flash')->streamGenerateContent($question);

        foreach ($result as $chunk) {
            echo $chunk->text();
            ob_flush();
            flush();
        }
    }

    public function getChatFromGeminiAPI($question)
    {


        $response = $chat->sendMessage('Create a story set in a quiet village in 1600s France');
        echo $response->text();
    }
}
