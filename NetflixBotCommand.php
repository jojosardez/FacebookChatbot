<?php

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
        return json_decode(file_get_contents('https://unogs.com/nf.cgi?u=vgrv6hjhe2tlajqj6o7cjnhe71&q='.urlencode($title)."&t=ns&cl=&st=bs&ob=&p=1&l=100&inc=&ao=and", false, $headerContext), true);
    }

    function getResponseTemplate($title) {
        return ["attachment"=>[
            "type"=>"template",
            "payload"=>[
                "template_type"=>"list",
                "top_element_style"=>"large",
                "elements"=>array(),
            "buttons"=>[
                [
                    "type"=>'web_url',
                    "url"=>'https://unogs.com/?q='.$title,
                    "title"=>"View All"
                ]
            ]
            ]
        ]];
    }

    function sendTitles($title, $titles) {        
        $responseTemplate = $this->getResponseTemplate($title);
        $titleCount = 0;
        while ($titleCount < 4 && $titleCount < $titles['COUNT']) {
            $responseTemplate["attachment"]["payload"]["elements"][] = [
                "title"=>html_entity_decode(strip_tags($titles['ITEMS'][$titleCount][1]), ENT_QUOTES)." (".ucfirst($titles['ITEMS'][$titleCount][6]).", ".$titles['ITEMS'][$titleCount][7].")",
                "image_url"=>$titles['ITEMS'][$titleCount][2],
                "subtitle"=>html_entity_decode(strip_tags($titles['ITEMS'][$titleCount][3]), ENT_QUOTES),
                "default_action"=>[
                    "type"=>"web_url",
                    "url"=>"https://unogs.com/video/?v=".$titles['ITEMS'][$titleCount][4]
                ]
            ];
            $titleCount++;
        }
        if ($titleCount == 1) {
            $responseTemplate["attachment"]["payload"]["template_type"] = "generic";
            $responseTemplate["attachment"]["payload"]["elements"][0]["buttons"] = [
                [
                    "type"=>'web_url',
                    "url"=>$responseTemplate["attachment"]["payload"]["elements"][0]["default_action"]["url"],
                    "title"=>"View More Details"
                ]
            ];
            unset($responseTemplate["attachment"]["payload"]["top_element_style"]);
            unset($responseTemplate["attachment"]["payload"]["buttons"]);
        }        
        $this->send($responseTemplate);
        return $titleCount;
    }

    function sendMultipleTitlesMessage($titlesSentCount, $totalTitleCount, $title) {
        if ($titlesSentCount <  $totalTitleCount){
            $this->send("Hey ".$this->user->getFirstName().", there were actually ".$totalTitleCount." movies and series that matched \"".$title."\". I just returned the 4 most relevant match but you can see them all when you click the \"View All\" button above.");
        }
    }
}