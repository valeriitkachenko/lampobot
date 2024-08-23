<?php

namespace App\Services;

use App\Commands\RegexCommand;
use Illuminate\Support\Collection;
use Telegram\Bot\Commands\Command;
use Telegram\Bot\Laravel\Facades\Telegram;
use Telegram\Bot\Objects\Update;

class TelegramWebhookService
{
    public const REGEX_FOR_STANDARD_COMMAND = '/^\/([^\s@]+)@?(\S+)?\s?(.*)$/';

    /**
     * @var Command[]
     */
    private array $commands;

    public function __construct()
    {
        $this->commands = Telegram::getCommands();
    }

    public function handleWebhook(): void
    {
        $update = Telegram::getWebhookUpdate();
        $message = $update->getMessage();

        $this->handle($message, $update);
    }

    private function handle(Collection $message, Update $update): void
    {
        if ($message->isEmpty() || empty($message->getText())) {
            return;
        }

        $commandName = $this->getCommandName($message->getText());

        if ($commandName) {
            $this->execute($commandName, $update);
        }
    }

    private function getCommandName(string $messageText): ?string
    {
        return $this->getStandardCommand($messageText) ?? $this->getRegexCommand($messageText);
    }

    private function getStandardCommand(string $message): ?string
    {
        if (preg_match(self::REGEX_FOR_STANDARD_COMMAND, $message, $matches)) {
            return $matches[1];
        }

        return null;
    }

    private function getRegexCommand(string $message): ?string
    {
        foreach ($this->getRegexCommands() as $name => $command) {
            if (preg_match($command->getPattern(), $message)) {
                return $name;
            }
        }

        return null;
    }

    /**
     * @return Collection|RegexCommand[]
     */
    private function getRegexCommands(): Collection
    {
        $regexCommands = collect();

        foreach($this->commands as $name => $command) {
            if ($command instanceof RegexCommand) {
                $regexCommands[$name] = $command;
            }
        }

        return $regexCommands;
    }

    public function execute(string $name, Update $update): mixed
    {
        if (!array_key_exists($name, $this->commands)) {
            return null;
        }

        return $this->commands[$name]->make(app('telegram.bot'), $update, []);
    }
}
