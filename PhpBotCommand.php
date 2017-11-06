<?php

class PhpBotCommand extends BotCommand {
    public function __construct() {
        parent::__construct("PHP");
    }

    protected function executeCommand($parameter) {
        return $this->getCommand()." command not yet implemented. Parameter passed: ".$parameter;
    }
}