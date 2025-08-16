<?php

namespace App\Services;

use Gemini\Data\Content;
use Gemini\Laravel\Facades\Gemini;
use Illuminate\Support\Facades\Log;

class GeminiService
{
    public function getAnswerFromGeminiAPI($question)
    {
        try {
            $chat = Gemini::chat(model: 'gemini-2.0-flash')
                ->startChat();
            $result = $chat->sendMessage($question);


                echo $result->text();

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
