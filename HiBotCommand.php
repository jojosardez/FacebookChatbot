<?php

class HiBotCommand extends BotCommand {
    public function __construct($sender, $user) {
        parent::__construct("HI", $sender, $user);
    }

    protected function executeCommand($parameter) {
        $this->send("Hello, ".$this->user->getFirstName()."!");
    }
}