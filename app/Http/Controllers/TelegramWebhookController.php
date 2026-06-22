<?php

namespace App\Http\Controllers;

use App\Services\TelegramService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class TelegramWebhookController extends Controller
{
    protected TelegramService $telegramService;

    public function __construct(TelegramService $telegramService)
    {
        $this->telegramService = $telegramService;
    }

    /**
     * Handle the incoming Telegram webhook request.
     */
    public function handle(Request $request): JsonResponse
    {
        $update = $request->all();

        if (!empty($update)) {
            $this->telegramService->handleUpdate($update);
        }

        return response()->json(['status' => 'success']);
    }
}
