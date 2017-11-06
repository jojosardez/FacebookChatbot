<?php

class PokedexBotCommand extends BotCommand {
    public function __construct() {
        parent::__construct("POKEDEX");
    }

    protected function executeCommand($parameter) {
        return $this->getCommand()." command not yet implemented. Parameter passed: ".$parameter;
    }
}