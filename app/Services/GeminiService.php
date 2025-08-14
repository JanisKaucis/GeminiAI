<?php
namespace App\Services;

use Gemini\Laravel\Facades\Gemini;
use Illuminate\Support\Facades\Log;

class GeminiService
{
    public function getAnswerFromGeminiAPI($question)
    {
        try {
            $result = Gemini::generativeModel(model: 'gemini-2.0-flash')->generateContent($question);

            return $result->text();
        }catch (\Exception $e){
             Log::error($e->getMessage());

            return false;
        }
    }
}
