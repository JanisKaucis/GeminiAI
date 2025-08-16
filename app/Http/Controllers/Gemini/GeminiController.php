<?php

namespace App\Http\Controllers\Gemini;

use App\Http\Controllers\Controller;
use App\Http\Requests\Gemini\GeminiRequest;
use App\Models\conversation;
use App\Services\GeminiService;
use Illuminate\Http\Request;
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
        $windowId = $validated['window_id'];
        try {
            $answer = $this->service->sendMessage($question, $windowId);
        } catch (\Exception $e) {
            return response()->json([
                'errors' => [
                    'failed' => 'Something went wrong!'
                ],
            ], 500);
        }

        return response()->json([
            'answer' => $answer,
        ]);
    }
}
