<?php

class CinemaBotCommand extends BotCommand {
    public function __construct() {
        parent::__construct("CINEMA");
    }

    protected function executeCommand($parameter) {
        return $this->getCommand()." command not yet implemented. Parameter passed: ".$parameter;
    }
}