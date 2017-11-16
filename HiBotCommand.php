<?php

/**
 * Class HiBotCommand
 * This class returns the greeting "Hello" with the sender's name.
 *
 * Usage:
 *  HI
 *
 * @author: Angelito Sardez, Jr.
 * @date: 9/11/2017
 */
class HiBotCommand extends BotCommand {
    public function __construct($sender, $user) {
        parent::__construct("HI", $sender, $user);
    }

    protected function executeCommand($parameter) {
        $this->send("Hello, ".$this->user->getFirstName()."!");
    }
}