<?php

namespace App\Commands;

use App\Services\DogService;
use Telegram\Bot\Commands\Command;

class DoggifCommand extends Command
{
    /**
     * @var string Command Name
     */
    protected $name = "doggif";

    /**
     * @var string Command Description
     */
    protected $description = "Random gif with a dog";

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
        $dogImageUrl = $this->dogService->getGifUrl();

        if (empty($dogImageUrl)) {
            return;
        }

        $this->replyWithDocument([
            'document' => $dogImageUrl,
            'reply_to_message_id' => $this->update->getMessage()->getMessageId()
        ]);
    }
}
