<?php

namespace App\Http\Controllers;

use App\Services\TelegramWebhookService;

class TelegramWebhookController extends Controller
{
    /**
     * @var TelegramWebhookService
     */
    protected $service;

    /**
     * @param TelegramWebhookService $service
     */
    public function __construct(TelegramWebhookService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $this->service->handleWebhook();
    }
}
