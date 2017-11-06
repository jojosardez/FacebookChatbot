<?php

class HiBotCommand extends BotCommand {
    public function __construct() {
        parent::__construct("HI");
    }

    protected function executeCommand($parameter) {
        return "Hello";
    }
}