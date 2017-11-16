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
        $pageNum = $this->getPageNum($parameter);
        $keyword = $this->getKeyword($pageNum, $parameter);

        if (trim($keyword) == "") {
            $this->sendTextWithHelp("Sorry ".$this->user->getFirstName().", you need to specify the Netflix movie or series title that you want to search.");
            return;
        }
       
        $titles = $this->queryTitles($pageNum, $keyword);
        if ($titles['COUNT'] == 0) {
            $this->send("No movie or series found with the title \"".$keyword."\", ".$this->user->getFirstName().". The movie or series you're looking for is not in Netflix.");            
        }
        else {
            $this->sendTitles($pageNum, $keyword, $titles);
        }
    }

    function getPageNum($parameter) {
        if (trim($parameter) == '') {
            return 1;
        }
        else {
            if (!strpos(trim($parameter), '~!@#')) {
                return 1;
            }
            else {
                return substr(trim($parameter), 0, strpos(trim($parameter), '~!@#'));
            }
        }
    }

    function getKeyword($pageNum, $parameter) {
        if (trim($parameter) == '') {
            return '';
        }
        else {
            return ltrim(trim($parameter), $pageNum.'~!@#');
        }
    }

    function queryTitles($pageNum, $title) {
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
        return json_decode(file_get_contents('https://unogs.com/nf.cgi?u=vgrv6hjhe2tlajqj6o7cjnhe71&q='.urlencode($title)."&t=ns&cl=&st=bs&ob=&p=".$pageNum."&l=8&inc=&ao=and", false, $headerContext), true);
    }

    function getResponseTemplate() {
        return ["attachment"=>[
            "type"=>"template",
            "payload"=>[
                "template_type"=>"generic",
                "elements"=>array()
            ]
        ]];
    }

    function getCountFrom($pageNum) {
        return (($pageNum - 1) * 8) + 1;
    }

    function getCountTo($pageNum, $currentCount, $totalCount) {
        $countTo = (($pageNum - 1) * 8) + $currentCount;
        if ($countTo > $totalCount) {
            $countTo = $totalCount;
        }
        return $countTo;
    }

    function getResultsCountRatioTerm($pageNum, $currentCount, $totalCount) {
        return $this->getCountFrom($pageNum)." to ".$this->getCountTo($pageNum, $currentCount, $totalCount)." of ".$totalCount;
    }

    function getSetRatioTerm($pageNum,  $totalCount) {
        return "Set ".$pageNum." of ".(ceil($totalCount / 8));
    }

    function sendTitles($pageNum, $title, $titles) {        
        $this->send("Displaying titles ".
            $this->getResultsCountRatioTerm($pageNum, count($titles['ITEMS']), $titles['COUNT']).
            " (".$this->getSetRatioTerm($pageNum, $titles['COUNT']).
            ") found in Netflix that matched \"".$title."\":");  
        $this->sendAction(SenderAction::typingOn);
        $responseTemplate = $this->getResponseTemplate();

        if ($pageNum > 1) {
            $responseTemplate["attachment"]["payload"]["elements"][] = [
                "title"=>'See previous results',
                "image_url"=>'https://is238-group5.cf/bot/images/BackArrow.jpg',
                "subtitle"=>'Display the previous or first set of titles.',
                "buttons"=>[
                    [
                        "type"=>'postback',
                        "title"=>'Previous Titles',
                        "payload"=>$this->command.' '.($pageNum - 1).'~!@#'.$title
                    ],
                    [
                        "type"=>'postback',
                        "title"=>'First Titles',
                        "payload"=>$this->command.' 1~!@#'.$title
                    ]
                ]
            ];
        }

        foreach ($titles['ITEMS'] as &$item) {
            $responseTemplate["attachment"]["payload"]["elements"][] = [
                "title"=>html_entity_decode(strip_tags($item[1]), ENT_QUOTES)." (".ucfirst($item[6]).", ".$item[7].")",
                "image_url"=>($item[2] == '') ? 'https://is238-group5.cf/bot/images/NoImageAvailable.jpg' : $item[2],
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
        }
        
        if (($titles['COUNT'] - ($pageNum * 8)) > 0) {
            $responseTemplate["attachment"]["payload"]["elements"][] = [
                "title"=>'See next results',
                "image_url"=>'https://is238-group5.cf/bot/images/NextArrow.jpg',
                "subtitle"=>'Display the next or last set of titles.',
                "buttons"=>[
                    [
                        "type"=>'postback',
                        "title"=>'Next Titles',
                        "payload"=>$this->command.' '.($pageNum + 1).'~!@#'.$title
                    ],
                    [
                        "type"=>'postback',
                        "title"=>'Last Titles',
                        "payload"=>$this->command.' '.(ceil($titles['COUNT'] / 8)).'~!@#'.$title
                    ]
                ]
            ];
        }

        $this->send($responseTemplate);
    }
}