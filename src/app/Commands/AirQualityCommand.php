<?php

namespace App\Commands;

use App\Services\AirQualityService;
use Telegram\Bot\Actions;

class AirQualityCommand extends RegexCommand
{

    protected string $name = "air-quality";

    protected string $description = "Air quality index";

    protected string $pattern = '/качество воздуха\s(.+)/ui';

    public function __construct(
        private readonly AirQualityService $service
    )
    {
    }

    public function handle()
    {
        $city = $this->getCityFromArguments($this->getArguments());

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
