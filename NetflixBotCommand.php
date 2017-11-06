<?php

class NetflixBotCommand extends BotCommand {
    public function __construct() {
        parent::__construct("NETFLIX");
    }

    protected function executeCommand($parameter) {
        return $this->getCommand()." command not yet implemented. Parameter passed: ".$parameter;
    }
}