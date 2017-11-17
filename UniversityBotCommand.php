<?php

/**
 * Class UniversityBotCommand
 * This class returns the official website of the given university.
 *
 * Usage:
 *  UNIVERSITY <search>
 *
 * @author: Ace Mangalino
 * @date: 17/11/2017
 */
class UniversityBotCommand extends BotCommand {
    public function __construct($sender, $user) {
        parent::__construct("UNIVERSITY", $sender, $user);
    }

    protected function executeCommand($parameter) {
        /**$this->send($this->command." command is not yet implemented, ".$this->user->getFirstName().". Parameter passed: ".$parameter);*/
		
		if (trim($parameter) == "") {
            $this->sendTextWithHelp("Sorry ".$this->user->getFirstName().", please type the keyword UNIVERSITY university name to search the website of the school, e.g. UNIVERSITY Manila");
            return;
        }
		
		$university = $this->queryUniversity($parameter);
		
        if (empty($university)) {
            $this->send("No university found, ".$this->user->getFirstName().". Please make sure the university exists.");
        }
        else { 
			/* $answer = ["attachment"=>[
				"type"=>"template",
				"payload"=>[
					"template_type"=>"button",
					"elements"=>[
						"buttons"=>[
							[
								"type"=>"web_url",
								"url"=>$university['web_pages'][0],
								"title"=>"Visit Website"
							],
						]
					]
				]
			]]; */
		
		
			$responseTemplate["attachment"]["payload"]["elements"][] = [
                "buttons"=>[
                    [
                        "type"=>'web_url',
                        "url"=>$university['web_pages'][0],
                        "title"=>'Visit Website'
                    ]
                ]
            ];
		
		
			//$this->send($university['name'].chr(10)."The website address: ".$university['web_pages'][0] . $responseTemplate);
			
			$this->send($university['name'].chr(10)."The website address: ".$university['web_pages'][0]);	
		}
		
    }
	
	function queryUniversity($name) {
		//return json_decode(file_get_contents('http://universities.hipolabs.com/search?name='.urlencode($name)), true);
		$universities = json_decode(file_get_contents('http://universities.hipolabs.com/search?name='.urlencode($name)), true);
		
		// extract university name given in $name by the user.
		foreach ($universities as &$university) {
			//if ($university['name'] == $name) {
                return $university;
            //}
        }
		
	}
}