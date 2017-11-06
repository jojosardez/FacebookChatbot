<?php

class UniversityBotCommand extends BotCommand {
    public function __construct() {
        parent::__construct("UNIVERSITY");
    }

    protected function executeCommand($parameter) {
        return $this->getCommand()." command not yet implemented. Parameter passed: ".$parameter;
    }
}