<?php

namespace App\Commands;

use App\Services\WeatherService;
use Telegram\Bot\Actions;

class WeatherCommand extends RegexCommand
{
    protected string $name = "weather";
    protected string $description = "Weather forecast";
    protected string $pattern = '/weather in\s(?<city>.+)/ui';

    public function __construct(
        private readonly WeatherService $weatherService
    ) {
    }

    public function handle(): void
    {
        $city = $this->getArguments()['city'];

        $this->replyWithChatAction(['action' => Actions::TYPING]);
        sleep(1);

        $this->replyWithMessage([
            'text' => $this->weatherService->getMessageWithWeatherForecast($city),
            'reply_to_message_id' => $this->update->getMessage()->getMessageId()
        ]);
    }
}
