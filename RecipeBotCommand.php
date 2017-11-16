<?php

/**
 * Class RecipeBotCommand
 * This class returns recipe and ingredients of the given viand.
 *
 * Usage:
 *  RECIPE <viand>
 *
 * @author: Archie Racadio
 * @date: 16/11/2017
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
        
            $template = ["attachment"=>[
                "type"=>"template",
                "payload"=>[
                    "template_type"=>"generic",
                    "elements"=>array()
                ]
            ]];
            $index = 0;
            while($index < sizeof($recipes['results']))
            {
                $template["attachment"]["payload"]["elements"][] = [
                    "title"=>$recipes['results'][$index]['title'],
                    "image_url"=>($recipes['results'][$index]['thumbnail'] == '') ? 'https://is238-group5.cf/bot/images/NoImageAvailable.png' : $recipes['results'][$index]['thumbnail'],
                    "subtitle"=>$recipes['results'][$index]['ingredients'],
                    "default_action"=>[
                        "type"=>"web_url",
                        "url"=>$recipes['results'][$index]['href']
                    ],
                    "buttons"=>[
                        [
                            "type"=>'web_url',
                            "url"=>$recipes['results'][$index]['href'],
                            "title"=>"View Details"
                        ]
                    ]
                ]; 
                $index++;
            }
            $this->send($template);
        }
    }
}