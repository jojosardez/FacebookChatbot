<?php

class WeatherBotCommand extends BotCommand {
    public function __construct() {
        parent::__construct("WEATHER");
    }

    protected function executeCommand($parameter) {
        return $this->getCommand()." command not yet implemented. Parameter passed: ".$parameter;
    }
}