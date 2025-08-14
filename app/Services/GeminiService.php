<?php
namespace App\Services;

use Gemini\Laravel\Facades\Gemini;

class GeminiService
{
    public function getAnswerFromGeminiAPI($question)
    {
        $result = Gemini::generativeModel(model: 'gemini-2.0-flash')->generateContent($question);

        return $result->text();
    }
}
