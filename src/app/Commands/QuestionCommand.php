<?php

namespace App\Commands;

use Exception;
use Telegram\Bot\Actions;

class QuestionCommand extends RegexCommand
{
    protected string $name = "question";
    protected string $description = "Question";
    protected string $pattern = '/Bot,\s(.+)\?/ui';

    /**
     * @throws Exception
     */
    public function handle(): void
    {
        $this->replyWithChatAction(['action' => Actions::TYPING]);
        sleep(1);

        $this->replyWithMessage([
            'text' => $this->receiveRandomAnswerFromTheUniverse(),
            'reply_to_message_id' => $this->update->getMessage()->getMessageId()
        ]);
    }

    /**
     * @throws Exception
     */
    private function receiveRandomAnswerFromTheUniverse(): string
    {
        return random_int(0,1) ? 'Yes' : 'No';
    }
}
