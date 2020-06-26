<?php

namespace App\Commands;

use App\Commands\Interfaces\RegexCommand;
use App\Traits\Commands\Regexable;
use Telegram\Bot\Actions;
use Telegram\Bot\Commands\Command;

class QuestionCommand extends Command implements RegexCommand
{
    use Regexable;

    /**
     * @var string Command Name
     */
    protected $name = "question";

    /**
     * @var string Command Description
     */
    protected $description = "Question";

    /**
     * @var string Regular expression pattern
     */
    protected $regexPattern = '/(Бот,)(.+)(\?)/im';

    /**
     * @inheritdoc
     */
    public function handle($arguments)
    {
        $this->replyWithChatAction(['action' => Actions::TYPING]);

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
