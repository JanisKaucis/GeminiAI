<?php

namespace App\Http\Controllers\Gemini;

use App\Http\Controllers\Controller;
use App\Http\Requests\Gemini\GeminiRequest;
use App\Services\GeminiService;
use Illuminate\Support\Facades\Log;

class GeminiController extends Controller
{
    private GeminiService $service;

    public function __construct(GeminiService $service)
    {
        $this->service = $service;
    }

    public function index(): \Inertia\Response|\Inertia\ResponseFactory
    {
        return inertia('gemini/Index');
    }

    public function getQuestion(GeminiRequest $request)
    {
        $validated = $request->validated();
        $question = $validated['question'];
        try {
            $result = $this->service->getAnswerFromGeminiAPI($question);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json([
                'error' => [
                    'failed' => 'Something went wrong!'
                    ],
            ], 500);
        }
        return response()->stream(function () use ($result) {
            echo $result;
        }, 200, [
            'Content-Type' => 'text/event-stream',
            'Cache-Control' => 'no-cache',
            'Connection' => 'keep-alive',
        ]);

    }
}
