<?php

namespace App\Services;

use Telegram\Bot\Laravel\Facades\Telegram;
use Telegram\Bot\Objects\Message;

class TelegramWebhookService
{
    public function handleWebhook()
    {
        $update = Telegram::getWebhookUpdates();
        $message = $update->getMessage();

        if ($this->userAskedBotAQuestion($message)) {
            return Telegram::sendMessage([
                'chat_id' => $message->getChat()->getId(),
                'text' => $this->getRandomAnswerYesOrNo(),
                'reply_to_message_id' => $message->getMessageId()
            ]);
        }
    }

    /**
     * @param Message $message
     * @return false|int
     */
    private function userAskedBotAQuestion(Message $message)
    {
        return preg_match('/(Бот,)(.+)(\?)/im', $message->getText());
    }

    /**
     * @return string
     */
    private function getRandomAnswerYesOrNo()
    {
        return rand(0,1) == 0 ? 'Да' : 'Нет';
    }
}
