<?php

namespace App\Http\Controllers;

use App\Services\TelegramWebhookService;

class TelegramWebhookController extends Controller
{
    public function __construct(private readonly TelegramWebhookService $service)
    {
    }

    public function index(): string
    {
        $this->service->handleWebhook();

        return 'ok';
    }
}
