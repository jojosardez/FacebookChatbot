<?php

class RemindBotCommand extends BotCommand {
    public function __construct() {
        parent::__construct("REMIND");
    }

    protected function executeCommand($parameter) {
        return $this->getCommand()." command not yet implemented. Parameter passed: ".$parameter;
    }
}