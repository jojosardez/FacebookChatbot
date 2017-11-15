<?php

/**
 * Class TrumpBotCommand
 * This class returns quotes by US Pres. Donald Trump.
 *
 * Usage:
 *  TRUMP
 *  TRUMP <keyword>
 *
 * @author: Jogie Lustre 
 * @date: 15/11/2017
 */
class TrumpBotCommand extends BotCommand {
    public function __construct($sender, $user) {
        parent::__construct("TRUMP", $sender, $user);
    }

	protected function executeCommand($parameter) {
		if (trim($parameter) == "") {
			$qoute = $this->queryRandomQoute();
			$this->send("Here's your random trump qoute ".$this->user->getFirstName()." : \"".$qoute['value']."\"");
            return;
        }else{
			if(strlen($parameter) >= 3){
				$qoute = $this->querySearchQoute($parameter);
				$this->send("Here's your trump qoutes using \"".$parameter."\" search string ".$this->user->getFirstName()."\n");
				$list = $qoute['_embedded']['quotes'];
				//echo "<pre>"; print_r($list); echo "</pre>";
				foreach($list as $item){
					foreach($item as $key=>$x){
						$this->send("\"".$x['value']."\"\n");
						
					}
					
				}

			}else{
				$qoute = $this->queryRandomQoute();
				$this->send("Search parameter should be at least 3 in lenght.\n Meanwhile, here's your random trump qoute ".$this->user->getFirstName()." : \"".$qoute['value']."\"");
			}
		}
    }
	
	function queryRandomQoute() {
        return json_decode(file_get_contents('https://api.tronalddump.io/random/quote'), true);
    }
	
	function querySearchQoute($param) {
        return json_decode(file_get_contents('https://api.tronalddump.io/search/quote?query='.$param), true);
    }
}