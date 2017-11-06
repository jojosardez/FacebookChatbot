<?php

class TrumpBotCommand extends BotCommand {
    public function __construct() {
        parent::__construct("TRUMP");
    }

    protected function executeCommand($parameter) {
        return $this->getCommand()." command not yet implemented. Parameter passed: ".$parameter;
    }
}