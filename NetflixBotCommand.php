<?php

class NetflixBotCommand extends BotCommand {
    public function __construct($sender, $user) {
        parent::__construct("NETFLIX", $sender, $user);
    }

    protected function executeCommand($parameter) {   
        if (trim($parameter) == "") {
            $this->send("Sorry ".$this->user->getFirstName().", you need to specify the movie or series title that you want to search.");
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
            $this->send($result['ITEMS'][0][1]." (".$result['ITEMS'][0][6].", ".$result['ITEMS'][0][7].")");
            $this->send($result['ITEMS'][0][2]);
            $this->send($result['ITEMS'][0][3]);
            $elements = array();
            $count = 0;
            while ($count < 4 && $count < $result['COUNT']) {
                $elements[] = array(
                    "title"=>$result['ITEMS'][$count][1]." (".$result['ITEMS'][$count][6].", ".$result['ITEMS'][$count][7].")",
                    "image_url"=>$result['ITEMS'][$count][2],
                    "subtitle"=>$result['ITEMS'][$count][3]
                );
                $count++;
            }
            $this->send("setting elements done");
            $this->send(sizeof($elements));
            $this->send(["attachment"=>[
                "type"=>"template",
                "payload"=>[
                    "template_type"=>"list",
                    "elements"=>$elements
                ],
                "buttons"=>[
                    [
                        "type"=>'web_url',
                        "url"=>'https://unogs.com/?q='.$parameter,
                        "title"=>"View Complete Search Result"
                    ]
                ]
                ]
                ]);
        }
    }
}