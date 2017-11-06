<?php

class IpBotCommand extends BotCommand {
    public function __construct() {
        parent::__construct("IP");
    }

    protected function executeCommand($parameter) {
        return $this->getCommand()." command not yet implemented. Parameter passed: ".$parameter;
    }
}