<?php

class EchoBotCommand extends BotCommand {
    public function __construct($sender, $user) {
        parent::__construct("ECHO", $sender, $user);
    }

    protected function executeCommand($parameter) {
        if ($parameter == "") {
            $this->send("Nothing to echo back, ".$this->user->getFirstName()."!");
        }
        else {
            $this->send($parameter);
        }
    }
}