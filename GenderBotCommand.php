<?php

class GenderBotCommand extends BotCommand {
    public function __construct() {
        parent::__construct("GENDER");
    }

    protected function executeCommand($parameter) {
        return $this->getCommand()." command not yet implemented. Parameter passed: ".$parameter;
    }
}