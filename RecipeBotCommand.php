<?php

class RecipeBotCommand extends BotCommand {
    public function __construct() {
        parent::__construct("RECIPE");
    }

    protected function executeCommand($parameter) {
        return $this->getCommand()." command not yet implemented. Parameter passed: ".$parameter;
    }
}