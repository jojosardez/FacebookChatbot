<?php

/**
 * Class RecipeBotCommand
 * This class returns recipe and ingredients of the given viand.
 *
 * Usage:
 *  RECIPE <viand>
 *
 * @author: Archie Racadio
 * @date: 14/11/2017
 */
class RecipeBotCommand extends BotCommand {
    public function __construct($sender, $user) {
        parent::__construct("RECIPE", $sender, $user);
    }

    protected function executeCommand($parameter) {
        if (trim($parameter) == "") {
            $this->send("Hi, ".$this->user->getFirstName().", what dish do you want to look at to know the ingredients. (e.g. Adobo)");
            return;
        }       
    $recipes = json_decode(file_get_contents('http://www.recipepuppy.com/api/?q='.urlencode($parameter).''), true);
if (sizeof($recipes['results']) == 0) {
           $this->send("Sorry, ".$this->user->getFirstName().", there is no recipe that matched your request. Please key in a valid or a specific name of dish you want to look at to know the ingredients. (e.g. CHICKEN Adobo)");
}
        else {
        
        $this->send($recipes['results'][0]['title'].$recipes['results'][0]['href'].$recipes['results'][0]['ingredients'].$recipes['results'][0]['thumbnail']);
        
                $test = '';
                foreach ($recipes['results'] as &$recipe) {
                $test = ".$recipe ['title']";
                $test = ".$recipe ['href']";
                $test = ".$recipe ['ingredients']";
                $test = ".$recipe ['thumbnail']";
            }     
                $this->send($test);
        }
    }
}