<?php

/**
 * Class GenderBotCommand
 * This class returns the probability of the gender of the given name.
 *
 * Usage:
 *  GENDER <Name>
 *
 * @author: Jogie Lustre 
 * @date: 15/11/2017
 */
class GenderBotCommand extends BotCommand {
    public function __construct($sender, $user) {
        parent::__construct("GENDER", $sender, $user);
    }

    protected function executeCommand($parameter) {
		if (trim($parameter) == "") {
			$parameter = $this->user->getFirstName();
        }

        $gender = $this->queryGender($parameter);

		//  {"name":"peter","gender":"male","probability":"1.00","count":796},
        if (empty($gender) || $gender['probability'] == 0 || $gender['gender'] == null) {
          $this->send("The name is unique. Gender is undetermined yet for \"".$parameter."\", ".$this->user->getFirstName().". Try again with other names.");
        }
        else {
          $this->send($gender['name']." is ".($gender['probability']*100)."% ".$gender['gender'].".");
        }
    }
	
	function queryGender($name) {
        return json_decode(file_get_contents('https://api.genderize.io/?name='.urlencode($name)), true);
    }
}