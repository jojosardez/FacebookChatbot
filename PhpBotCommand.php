<?php

/**
 * Class PhpBotCommand
 * This class returns foreign currency exchange rate.
 *
 * Usage:
 *  PHP <currency>
 *
 * @author: Ace Mangalino
 * @date: 17/11/2017
 */
class PhpBotCommand extends BotCommand {
    public function __construct($sender, $user) {
        parent::__construct("PHP", $sender, $user);
    }

    protected function executeCommand($parameter) {
        if (trim($parameter) == "") {
            $this->sendTextWithHelp("Sorry ".$this->user->getFirstName().", please type the keyword PHP target currency to see the current exhange rate, e.g. PHP SGD");
            return;
        }

        $rate = json_decode(file_get_contents('https://api.fixer.io/latest?base='.strtoUpper(trim($parameter)).'&symbols=PHP'), true);
        if (count($rate['rates']) == 0) {
            $this->send("No exchange rates found. Please make sure to provide valid currency code.");
        }
        else {
            $this->send("1 ".strtoUpper(trim($parameter))." = ".$rate['rates']['PHP']." PHP");
        }
        
    }
}