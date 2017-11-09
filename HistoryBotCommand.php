<?php

class HistoryBotCommand extends BotCommand {
    public function __construct($sender, $user) {
        parent::__construct("HISTORY", $sender, $user);
    }

    protected function executeCommand($parameter) {
        
        if($parameter ==''){
            $cc = curl_init();
            curl_setopt($cc, CURLOPT_URL, "http://numbersapi.com/random/date");
            curl_setopt($cc, CURLOPT_RETURNTRANSFER, 1);
            $result = curl_exec($cc);
            curl_close($cc);
            
            $this->send($result);
        }else if($parameter =='today'){
            $cc = curl_init();
            curl_setopt($cc, CURLOPT_URL, "http://numbersapi.com/".date("m")."/".date("d")."/date");
            curl_setopt($cc, CURLOPT_RETURNTRANSFER, 1);
            $result = curl_exec($cc);
            curl_close($cc);
            
            $this->send($result);
        
        }else if(sizeof(explode('-',$parameter)) == 2 
                && is_numeric(explode('-',$parameter)[0])
                && is_numeric(explode('-',$parameter)[1])
                && explode('-',$parameter)[0] < 13
                && explode('-',$parameter)[0] > 0
                && explode('-',$parameter)[1] < 32
                && explode('-',$parameter)[1] > 0
        ){
            $cc = curl_init();
            curl_setopt($cc, CURLOPT_URL, "http://numbersapi.com/".explode('-',$parameter)[0]."/".explode('-',$parameter)[1]."/date");
            curl_setopt($cc, CURLOPT_RETURNTRANSFER, 1);
            $result = curl_exec($cc);
            curl_close($cc);
            
            $this->send($result);
        
        }else{
            $this->send($this->getCommand()." '$parameter' is not a valid input.".chr(10).
                    "Proper formats:".chr(10).
                    $this->getCommand().chr(10).
                    $this->getCommand()." today".chr(10).
                    $this->getCommand()." MM-DD".chr(10)); 
        }

    }
}