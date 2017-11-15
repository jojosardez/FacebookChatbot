<?php

/**
 * Class HelpBotCommand
 * This class returns bot command usage information.
 *
 * Usage:
 *  HELP
 *  HELP <COMMAND>
 *
 * @author: TBD
 * @date: 11/11/2017
 */
class HelpBotCommand extends BotCommand {
    public function __construct($sender, $user) {
        parent::__construct("HELP", $sender, $user);
    }

    protected function executeCommand($parameter) {
        $this->send($this->command." command is not yet implemented, ".$this->user->getFirstName().". Parameter passed: ".$parameter);
    }
}