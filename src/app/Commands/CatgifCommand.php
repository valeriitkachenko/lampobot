<?php

namespace App\Commands;

use App\Services\CatService;
use Telegram\Bot\Commands\Command;

class CatgifCommand extends Command
{
    /**
     * @var string Command Name
     */
    protected $name = "catgif";

    /**
     * @var string Command Description
     */
    protected $description = "Random gif with a cat :3";

    /**
     * @var CatService
     */
    private $catService;

    /**
     * WeatherCommand constructor.
     */
    public function __construct()
    {
        $this->catService = app()->make(CatService::class);
    }

    /**
     * @inheritdoc
     */
    public function handle($arguments)
    {
        $catImageUrl = $this->catService->getCatGifUrl();

        if (empty($catImageUrl)) {
            return;
        }

        $this->replyWithDocument([
            'document' => $catImageUrl,
            'reply_to_message_id' => $this->update->getMessage()->getMessageId()
        ]);
    }
}
