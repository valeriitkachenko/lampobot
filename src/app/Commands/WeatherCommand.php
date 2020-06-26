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

    /**
     * @var string Command Name
     */
    protected $name = "weather";

    /**
     * @var string Command Description
     */
    protected $description = "Weather forecast";

    /**
     * @var string Regular expression pattern
     */
    protected $regexPattern = '/погода\s(.+)/ui';

    /**
     * @var WeatherService
     */
    private $weatherService;

    /**
     * WeatherCommand constructor.
     */
    public function __construct()
    {
        $this->weatherService = app()->make(WeatherService::class);
    }

    /**
     * @inheritdoc
     */
    public function handle($arguments)
    {
        $city = $arguments[1];

        $this->replyWithChatAction(['action' => Actions::TYPING]);
        sleep(1);

        $this->replyWithMessage([
            'text' => $this->weatherService->getMessageWithWeatherForecast($city),
            'reply_to_message_id' => $this->update->getMessage()->getMessageId()
        ]);
    }
}
