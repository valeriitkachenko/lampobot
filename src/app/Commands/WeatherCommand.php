<?php

namespace App\Commands;

use App\Commands\Interfaces\RegexCommand;
use App\Services\WeatherService;
use App\Traits\Commands\Regexable;
use Telegram\Bot\Actions;
use Telegram\Bot\Commands\Command;

class WeatherCommand extends Command implements RegexCommand
{
    use Regexable;

    protected string $name = "weather";

    protected string $description = "Weather forecast";

    protected string $regexPattern = '/погода\s(.+)/ui';

    public function __construct(
        private readonly WeatherService $weatherService
    ) {
    }

    public function handle()
    {
        $city = $this->getArguments()[1];

        $this->replyWithChatAction(['action' => Actions::TYPING]);
        sleep(1);

        $this->replyWithMessage([
            'text' => $this->weatherService->getMessageWithWeatherForecast($city),
            'reply_to_message_id' => $this->update->getMessage()->getMessageId()
        ]);
    }
}
