<?php

namespace App\Commands;

use App\Commands\Interfaces\RegexCommand;
use App\Services\AirQualityService;
use App\Traits\Commands\Regexable;
use Telegram\Bot\Actions;
use Telegram\Bot\Commands\Command;

class AirQualityCommand extends Command implements RegexCommand
{
    use Regexable;

    /**
     * @var string Command Name
     */
    protected $name = "air-quality";

    /**
     * @var string Command Description
     */
    protected $description = "Air quality index";

    /**
     * @var string Regular expression pattern
     */
    protected $regexPattern = '/качество воздуха\s(.+)/ui';

    /**
     * @var AirQualityService
     */
    private $service;

    /**
     * AirQualityCommand constructor.
     */
    public function __construct()
    {
        $this->service = app()->make(AirQualityService::class);
    }

    /**
     * @inheritdoc
     */
    public function handle($arguments)
    {
        $city = $this->getCityFromArguments($arguments);

        if ($this->cityIsNotSupported($city)) {
            return $this->sendToBeImplementedMessage();
        }

        $this->replyWithChatAction(['action' => Actions::TYPING]);
        sleep(1);

        $this->replyWithMessage([
            'text' => $this->service->getMessageWithAirQualityIndex('Kyiv', 'Kyiv', 'Ukraine'),
            'reply_to_message_id' => $this->update->getMessage()->getMessageId()
        ]);
    }

    private function getCityFromArguments(array $arguments): string
    {
        return $arguments[1];
    }

    /**
     * TODO: temporary implementation, need to add support of other cities as well
     */
    private function cityIsNotSupported(string $city): bool
    {
        return !in_array(mb_strtolower($city), ['kyiv', 'киев', 'kiev', 'київ']);
    }

    private function sendToBeImplementedMessage()
    {
        $this->replyWithMessage([
            'text' => 'Индекс качества воздуха на данный момент доступен только для Киева',
            'reply_to_message_id' => $this->update->getMessage()->getMessageId()
        ]);
    }

}
