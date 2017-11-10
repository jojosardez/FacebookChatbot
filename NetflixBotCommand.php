<?php

class NetflixBotCommand extends BotCommand {
    public function __construct($sender, $user) {
        parent::__construct("NETFLIX", $sender, $user);
    }

    protected function executeCommand($parameter) {   
        if (trim($parameter) == "") {
            $this->send("Sorry ".$this->user->getFirstName().", you need to specify the Netflix movie or series title that you want to search.");
            return;
        }

        $header = [
            "http" => [
                "method" => "GET",
                "header" => "Accept: application/json, text/javascript, */*; q=0.01\r\n".
                    "Accept-Encoding: gzip, deflate, br\r\n".
                    "Accept-Language: en-US,en;q=0.9\r\n".
                    "Connection: keep-alive\r\n".
                    "Host: unogs.com\r\n".
                    "Referer: https://unogs.com/?q=".urlencode($parameter)."&st=b\r\n".
                    "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/62.0.3202.89 Safari/537.36\r\n".
                    "X-Requested-With: XMLHttpRequest"
            ]
        ];
        $headerContext = stream_context_create($header);
        $result = json_decode(file_get_contents('https://unogs.com/nf.cgi?u=vgrv6hjhe2tlajqj6o7cjnhe71&q='.urlencode($parameter)."&t=ns&cl=&st=bs&ob=&p=1&l=100&inc=&ao=and", false, $headerContext), true);

        if ($result['COUNT'] == 0) {
            $this->send("No movie or series found with the title \"".$parameter."\", ".$this->user->getFirstName().". The movie or series you're looking for is not in Netflix.");            
        }
        else {
            $resultResponse = ["attachment"=>[
                "type"=>"template",
                "payload"=>[
                    "template_type"=>"list",
                    "top_element_style"=>"large",
                    "elements"=>array(),
                "buttons"=>[
                    [
                        "type"=>'web_url',
                        "url"=>'https://unogs.com/?q='.$parameter,
                        "title"=>"View All"
                    ]
                ]
                ]
            ]];
            $count = 0;
            while ($count < 4 && $count < $result['COUNT']) {
                $resultResponse["attachment"]["payload"]["elements"][] = [
                    "title"=>html_entity_decode(strip_tags($result['ITEMS'][$count][1]), ENT_QUOTES)." (".ucfirst($result['ITEMS'][$count][6]).", ".$result['ITEMS'][$count][7].")",
                    "image_url"=>$result['ITEMS'][$count][2],
                    "subtitle"=>html_entity_decode(strip_tags($result['ITEMS'][$count][3]), ENT_QUOTES),
                    "default_action"=>[
                        "type"=>"web_url",
                        "url"=>"https://unogs.com/video/?v=".$result['ITEMS'][$count][4]
                    ]
                ];
                $count++;
            }
            if ($count == 1) {
                $resultResponse["attachment"]["payload"]["template_type"] = "generic";
                $resultResponse["attachment"]["payload"]["elements"][0]["buttons"] = [
                    [
                        "type"=>'web_url',
                        "url"=>$resultResponse["attachment"]["payload"]["elements"][0]["default_action"]["url"],
                        "title"=>"View More Details"
                    ]
                ];
                unset($resultResponse["attachment"]["payload"]["top_element_style"]);
                unset($resultResponse["attachment"]["payload"]["buttons"]);
            }
            $this->send($resultResponse);
            if ($count <  $result['COUNT']){
                $this->send("Hey ".$this->user->getFirstName().", there were actually ".$result['COUNT']." movies and series that matched \"".$parameter."\". I just returned the 4 most relevant match but you can see them all when you click the \"View All\" button above.");
            }
        }
    }
}