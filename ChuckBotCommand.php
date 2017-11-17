<?php

/**
 * Class chuck
 * This class returns chuck norris jokes.
 *
 * Usage:
 *  chuck
 *
 * @author: Jogie Lustre 
 * @date: 17/11/2017
 */
class ChuckBotCommand extends BotCommand {
    public function __construct($sender, $user) {
        parent::__construct("CHUCK", $sender, $user);
    }

	protected function executeCommand($parameter = null) {
		$chuck = $this->queryRandomMeme();
		$this->send("Here's your chuck norris joke ".$this->user->getFirstName().": \"".$chuck['value']."\"");
				
    }
		
	function queryRandomMeme() {
        return json_decode(file_get_contents('https://api.chucknorris.io/jokes/random'), true);
    }
}