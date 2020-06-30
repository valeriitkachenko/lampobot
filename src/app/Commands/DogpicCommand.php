<?php

namespace App\Commands;

use App\Services\DogService;
use Telegram\Bot\Commands\Command;

class DogpicCommand extends Command
{
    /**
     * @var string Command Name
     */
    protected $name = "dogpic";

    /**
     * @var string Command Description
     */
    protected $description = "Random pic of a dog";

    /**
     * @var DogService
     */
    private $dogService;

    /**
     * WeatherCommand constructor.
     */
    public function __construct()
    {
        $this->dogService = app()->make(DogService::class);
    }

    /**
     * @inheritdoc
     */
    public function handle($arguments)
    {
        $dogImageUrl = $this->dogService->getPicUrl();

        if (empty($dogImageUrl)) {
            return;
        }

        $this->replyWithPhoto([
            'photo' => $dogImageUrl,
            'reply_to_message_id' => $this->update->getMessage()->getMessageId()
        ]);
    }
}
