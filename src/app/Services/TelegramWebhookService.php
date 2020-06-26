<?php

namespace App\Services;

use App\Commands\Interfaces\RegexCommand;
use Illuminate\Support\Collection;
use Telegram\Bot\Commands\Command;
use Telegram\Bot\Laravel\Facades\Telegram;
use Telegram\Bot\Objects\Update;

class TelegramWebhookService
{
    const REGEX_FOR_STANDARD_COMMAND = '/^\/([^\s@]+)@?(\S+)?\s?(.*)$/';

    /**
     * @var Command[]
     */
    private $commands = [];

    public function __construct()
    {
        $this->commands = Telegram::getCommands();
    }

    public function handleWebhook()
    {
        $update = Telegram::getWebhookUpdates();
        $message = $update->getMessage();

        $this->handle($message, $update);
    }

    /**
     * Handles Inbound Messages and Executes Appropriate Command.
     *
     * @param $message
     * @param $update
     * @return Update
     */
    private function handle($message, Update $update)
    {
        $this->throwErrorIfMessageIsEmpty($message);

        $commandAndArguments = $this->getCommandAndArguments($message->getText());

        if (!empty($commandAndArguments)) {
            $this->execute($commandAndArguments['command'], $commandAndArguments['arguments'], $update);
        }

        return $update;
    }

    /**
     * @param string $message
     */
    private function throwErrorIfMessageIsEmpty($message)
    {
        if (trim($message) === '') {
            throw new \InvalidArgumentException('Message is empty, Cannot parse for command');
        }
    }

    /**
     * @param string $message
     * @return array
     */
    private function getCommandAndArguments($message)
    {
        $matches = $this->commandIsStandardOne($message);

        if (empty($matches)) {
            $matches = $this->commandIsRegexOne($message);
        }

        return $matches;
    }

    /**
     * @param $message
     * @return array|false
     */
    private function commandIsStandardOne($message)
    {
        if (preg_match(self::REGEX_FOR_STANDARD_COMMAND, $message, $matches)) {
            return [
                'command' => $matches[1],
                'arguments' => $matches[3],
            ];
        }

        return false;
    }

    /**
     * @param string $message
     * @return array|false
     */
    private function commandIsRegexOne($message)
    {
        foreach ($this->getRegexCommands() as $name => $command) {
            if (preg_match($command->getRegexPattern(), $message, $matches)) {
                return [
                    'command' => $name,
                    'arguments' => $matches,
                ];
            }
        }

        return false;
    }

    /**
     * @return Collection
     */
    private function getRegexCommands()
    {
        $regexCommands = collect();

        foreach($this->commands as $name => $command) {
            if ($command instanceof RegexCommand) {
                $regexCommands[$name] = $command;
            }
        }

        return $regexCommands;
    }

    /**
     * Execute the command.
     *
     * @param $name
     * @param $arguments
     * @param $message
     * @return mixed
     */
    public function execute($name, $arguments, $message)
    {
        if (array_key_exists($name, $this->commands)) {
            return $this->commands[$name]->make(telegram(), $arguments, $message);
        } elseif (array_key_exists('help', $this->commands)) {
            return $this->commands['help']->make(telegram(), $arguments, $message);
        }

        return true;
    }
}
