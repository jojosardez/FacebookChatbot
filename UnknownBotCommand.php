<?php

class UnknownBotCommand extends BotCommand {
    public function __construct($command, $sender, $user) {
        parent::__construct($command, $sender, $user);
    }

    protected function executeCommand($parameter) {
        $this->send("Unknown command \"".$this->command."\" was received, ".$this->user->getFirstName().".");
    }
}