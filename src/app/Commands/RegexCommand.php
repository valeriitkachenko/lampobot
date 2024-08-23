<?php

namespace App\Commands;

use Telegram\Bot\Commands\Command;

abstract class RegexCommand extends Command
{
    protected function parseCommandArguments(): array
    {
        if ($this->pattern === '') {
            return [];
        }

        preg_match($this->pattern, $this->getUpdate()->getMessage()->text, $matches);

        return array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
    }
}
