<?php

namespace App\Commands;

use App\Commands\Interfaces\RegexCommand;
use App\Traits\Commands\Regexable;
use Telegram\Bot\Actions;
use Telegram\Bot\Commands\Command;

class QuestionCommand extends Command implements RegexCommand
{
    use Regexable;

    protected string $name = "question";

    protected string $description = "Question";

    protected string $regexPattern = '/Бот,\s(.+)\?/ui';

    public function handle()
    {
        $this->replyWithChatAction(['action' => Actions::TYPING]);
        sleep(1);

        $this->replyWithMessage([
            'text' => $this->getRandomAnswerYesOrNo(),
            'reply_to_message_id' => $this->update->getMessage()->getMessageId()
        ]);
    }

    /**
     * @return string
     */
    private function getRandomAnswerYesOrNo()
    {
        return rand(0,1) == 0 ? 'Да' : 'Нет';
    }
}
