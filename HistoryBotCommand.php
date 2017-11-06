<?php

class HistoryBotCommand extends BotCommand {
    public function __construct() {
        parent::__construct("HISTORY");
    }

    protected function executeCommand($parameter) {
        return $this->getCommand()." command not yet implemented. Parameter passed: ".$parameter;
    }
}