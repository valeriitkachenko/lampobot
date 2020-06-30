<?php

namespace App\Commands;

use App\Services\CatService;
use Telegram\Bot\Commands\Command;

class CatpicCommand extends Command
{
    /**
     * @var string Command Name
     */
    protected $name = "catpic";

    /**
     * @var string Command Description
     */
    protected $description = "Random picture of a cat :3";

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
        $catImageUrl = $this->catService->getCatPicUrl();

        if (empty($catImageUrl)) {
            return;
        }

        $this->replyWithPhoto([
            'photo' => $catImageUrl,
            'reply_to_message_id' => $this->update->getMessage()->getMessageId()
        ]);
    }
}
