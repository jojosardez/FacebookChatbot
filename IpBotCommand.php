<?php

/**
 * Class IpBotCommand
 * This class returns information about the given IP address.
 *
 * Usage:
 *  IP <IP address>
 *
 * @author: Archie Racadio
 * @date: 16/11/2017
 */
class IpBotCommand extends BotCommand {
    public function __construct($sender, $user) {
       parent::__construct("IP", $sender, $user);
    }

    protected function executeCommand($parameter) {

        $this->getIPDetails($parameter);
    }
  

    protected function getIPDetails($ip) {

        $loc = json_decode(file_get_contents("https://ipapi.co/{$ip}/json"));
    
        $formatted = "";
        foreach ($loc as $key => $value){
            if($value != '') $formatted.=ucfirst(str_replace("_"," ",$key))." : ".str_replace("_"," ",$value).chr(10);
        }
        $this->send($formatted);
   }
}