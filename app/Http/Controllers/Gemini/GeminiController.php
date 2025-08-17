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
        $conversations = $this->service->getConversations();
        return inertia('gemini/Index', ['conversations' => $conversations]);
    }

    public function getQuestion(GeminiRequest $request): \Illuminate\Http\JsonResponse
    {
        $validated = $request->validated();
        $question = $validated['question'];
        $windowId = $validated['window_id'];
        try {
            $answer = $this->service->sendMessage($question, $windowId);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
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

    public function getChatWindowHistory(Request $request): \Illuminate\Http\JsonResponse
    {
        $windowId = $request->get('window_id');

        $messages = $this->service->getChatHistory($windowId);

        return response()->json([
            'messages' => $messages,
        ]);
    }

    public function getConversationsHistory(): \Illuminate\Http\JsonResponse
    {
        $conversations = $this->service->getConversations();

        return response()->json([
            'conversations' => $conversations
        ]);
    }
}
