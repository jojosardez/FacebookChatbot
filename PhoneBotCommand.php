<?php

class PhoneBotCommand extends BotCommand {
    public function __construct() {
        parent::__construct("PHONE");
    }

    protected function executeCommand($parameter) {
        return $this->getCommand()." command not yet implemented. Parameter passed: ".$parameter;
    }
}