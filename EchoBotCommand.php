<?php

class EchoBotCommand extends BotCommand {
    public function __construct() {
        parent::__construct("ECHO");
    }

    protected function executeCommand($parameter) {        
        if ($parameter == "") {
            return "Nothing to echo back!";
        }
        else {
            return $parameter;
        }
    }
}