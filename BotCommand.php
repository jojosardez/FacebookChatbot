<?php

abstract class BotCommand {
    public function __construct($command, $sender, $user) {
        $this->command = $command;
        $this->sender = $sender;
        $this->user = $user;
    }

    protected $command;   
    protected $sender;   
    protected $user;   
    abstract protected function executeCommand($parameter);

    public function execute($parameter) {
        $this->executeCommand($parameter);
    }

    protected function send($message) {
        $this->sender->send($message);
    }
}