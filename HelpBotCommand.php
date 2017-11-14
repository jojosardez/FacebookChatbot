<?php

class HelpBotCommand extends BotCommand {
    public function __construct($sender, $user) {
        parent::__construct("HELP", $sender, $user);
    }

    protected function executeCommand($parameter) {
        $this->send($this->command." command is not yet implemented, ".$this->user->getFirstName().". Parameter passed: ".$parameter);
    }
}