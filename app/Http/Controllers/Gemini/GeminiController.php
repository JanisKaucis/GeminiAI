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

    public function postQuestion(GeminiRequest $request): \Illuminate\Http\JsonResponse
    {
        $validated = $request->validated();
        $question = $validated['question'];

        $answer = $this->service->getAnswerFromGeminiAPI($question);

        if (!$answer) {
            return response()->json([
                'errors' => [
                    'question' => [
                        'Something went wrong!'
                    ]
                ],
            ], 400);
        }
        return response()->json([
            'answer' => $answer,
        ]);
    }
}
