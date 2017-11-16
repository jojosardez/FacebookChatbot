<?php

/**
 * Class NetflixBotCommand
 * This class returns list of movies or series available in Netflix, from the given movie or series name.
 *
 * Usage:
 *  NETFLIX <Movie or Series Name>
 *
 * @author: Angelito Sardez, Jr.
 * @date: 16/11/2017
 */
class NetflixBotCommand extends BotCommand {
    public function __construct($sender, $user) {
        parent::__construct("NETFLIX", $sender, $user);
    }

    protected function executeCommand($parameter) {   
        if (trim($parameter) == "") {
            $this->sendTextWithHelp("Sorry ".$this->user->getFirstName().", you need to specify the Netflix movie or series title that you want to search.");
            return;
        }
       
        $titles = $this->queryTitles($parameter);
        if ($titles['COUNT'] == 0) {
            $this->send("No movie or series found with the title \"".$parameter."\", ".$this->user->getFirstName().". The movie or series you're looking for is not in Netflix.");            
        }
        else {
            $titlesSentCount = $this->sendTitles($parameter, $titles);
            $this->sendMultipleTitlesMessage($titlesSentCount, $titles['COUNT'], $parameter);
        }
    }

    function queryTitles($title) {
        $header = [
            "http" => [
                "method" => "GET",
                "header" => "Accept: application/json, text/javascript, */*; q=0.01\r\n".
                    "Accept-Encoding: gzip, deflate, br\r\n".
                    "Accept-Language: en-US,en;q=0.9\r\n".
                    "Connection: keep-alive\r\n".
                    "Host: unogs.com\r\n".
                    "Referer: https://unogs.com/?q=".urlencode($title)."&st=b\r\n".
                    "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/62.0.3202.89 Safari/537.36\r\n".
                    "X-Requested-With: XMLHttpRequest"
            ]
        ];
        $headerContext = stream_context_create($header);
        return json_decode(file_get_contents('https://unogs.com/nf.cgi?u=vgrv6hjhe2tlajqj6o7cjnhe71&q='.urlencode($title)."&t=ns&cl=&st=bs&ob=&p=1&l=8&inc=&ao=and", false, $headerContext), true);
    }

    function getResponseTemplate($title) {
        return ["attachment"=>[
            "type"=>"template",
            "payload"=>[
                "template_type"=>"generic",
                "elements"=>array()
            ]
        ]];
    }

    function sendTitles($title, $titles) {        
        $responseTemplate = $this->getResponseTemplate($title);
        $titleCount = 0;
        foreach ($titles['ITEMS'] as &$item) {
            $responseTemplate["attachment"]["payload"]["elements"][] = [
                "title"=>html_entity_decode(strip_tags($item[1]), ENT_QUOTES)." (".ucfirst($item[6]).", ".$item[7].")",
                "image_url"=>$item[2],
                "subtitle"=>html_entity_decode(strip_tags($item[3]), ENT_QUOTES),
                "default_action"=>[
                    "type"=>'web_url',
                    "url"=>'https://unogs.com/video/?v='.$item[4]
                ],
                "buttons"=>[
                    [
                        "type"=>'web_url',
                        "url"=>'https://unogs.com/video/?v='.$item[4],
                        "title"=>'View Details'
                    ]
                ]
            ];
            $titleCount++;
        }      
        $this->send($responseTemplate);
        return $titleCount;
    }

    function sendMultipleTitlesMessage($titlesSentCount, $totalTitleCount, $title) {
        if ($titlesSentCount <  $totalTitleCount){
            $this->send("Hey ".$this->user->getFirstName().", there were actually ".$totalTitleCount." movies and series that matched \"".$title."\". I just returned the 8 most relevant match but you can see them all when you click the \"View All\" button above.");
        }
    }
}