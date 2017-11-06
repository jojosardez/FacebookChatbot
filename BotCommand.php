<?php

abstract class BotCommand {
    public function __construct($command) {
        $this->command   = $command;
    }

    protected $command;   
    abstract protected function executeCommand($parameter);

    protected function getCommand() {
        return $this->command;
    }

    public function execute($parameter) {
        return $this->executeCommand($parameter);
    }
}