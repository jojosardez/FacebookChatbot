<?php

/**
 * Class PhoneBotCommand
 * This class returns information about the given phone number.
 *
 * Usage:
 *  PHONE <number>
 *
 * @author: Kasey Martin
 * @date: 9/11/2017
 */
class PhoneBotCommand extends BotCommand {
    public function __construct($sender, $user) {
        parent::__construct("PHONE", $sender, $user);
    }

    protected function executeCommand($parameter) {
        $access_key = '68b6926ce07f90e78b3944a45759a86e';
        
        $cc = curl_init();
        curl_setopt($cc, CURLOPT_URL, "http://apilayer.net/api/validate?access_key=$access_key&number=$parameter&format=1");
        curl_setopt($cc, CURLOPT_RETURNTRANSFER, 1);
        $result = json_decode(curl_exec($cc));
        curl_close($cc);
        if(array_key_exists('valid',$result)){
        	if($result->valid){
        		$formatted = "";
        		foreach ($result as $key => $value){
        			if($value != '') $formatted.=ucfirst(str_replace("_"," ",$key))." : ".$value.chr(10);
        		}
        		$this->send($formatted);
        	}
        	else{
        		$this->send("Sorry $parameter is not a valid phone number. Although I am a simple robot, so maybe I'm mistaken? Please try putting a more complete number that starts with the country code :).");
        	}
        }else{
        	
        	$this->send($result->error->info);
        }
    }
}
