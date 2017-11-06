<?php

class UnknownBotCommand extends BotCommand {
    public function __construct($command) {
        parent::__construct($command);
    }

    protected function executeCommand($parameter) {
        return 'Unknown command "'.$this->getCommand().'" was received.';
    }
}