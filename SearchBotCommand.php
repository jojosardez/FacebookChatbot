<?php

class SearchBotCommand extends BotCommand {
    public function __construct($command, $sender, $user) {
        parent::__construct($command, $sender, $user);
    }

    protected function executeCommand($parameter) {
        $pageNum = $this->getPageNum($parameter);
        $keyword = $this->getKeyword($pageNum, $parameter);
        if (!$this->isTrending() && trim($keyword) == "" && $pageNum == 1) {
            $this->sendTextWithHelp("Hey ".$this->user->getFirstName().", you didn't specify any terms to search. I will give you the currently trending ".($this->isSearch() ? "topics" : "news")." then. You may also want to click \"See ".$this->command." Help\" button below to know more about searching ".($this->isSearch() ? "topics" : "news articles").".");
            $this->sendAction(SenderAction::typingOn);
        }
       
        $searchResults = $this->search($pageNum, $keyword);
        if ($searchResults['count'] == 0) {
            if ($this->isTrending()) {
                $this->send("No trending topics found, ".$this->user->getFirstName().". Please try again later.");            
            }
            else {
                $this->send("No relevant ".($this->isSearch() ? "topics" : "news articles")." found for the keyword \"".$keyword."\", ".$this->user->getFirstName().". Please try again with a different keyword.");            
            }
        }
        else {
            if ($this->isTrending()) {                
                $this->sendTrendResults($searchResults);
            }
            else {
                $this->sendSearchResults($pageNum, $keyword, $searchResults);
            }
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

    function isTrending() {
        return strtolower($this->command) == "trending";
    }
    
    function isSearch() {
        return strtolower($this->command) == "search";
    }

    function search($pageNum, $keyword) {
        $header = [
            "http" => [
                "method" => "GET",
                "header" => "Host: www.faroo.com\r\n".
                    "Referer: http://www.faroo.com/hp/api/api.html"
            ]
        ];
        $headerContext = stream_context_create($header);
        return json_decode(file_get_contents('http://www.faroo.com/api?q='.($this->isTrending() ? '' : urlencode($keyword)).'&start='.$pageNum.'&length='.($this->isTrending() ? '10' : '4').'&l=en&src='.$this->getApiSrc($keyword).'&f=json', false, $headerContext), true);
    }

    function getApiSrc($keyword) {
        if ($this->isTrending()) {
            return "trends";
        }
        else {
            return $this->isSearch() ? 'web' : 'news';
        }
    }

    function getButtonResponseTemplate($index, $topic) {
        return ["attachment"=>[
            "type"=>"template",
            "payload"=>[
                "template_type"=>"button",
                "text"=>$index.'. '.$topic,
            "buttons"=>[
                [
                    "type"=>'postback',
                    "title"=>'Search '.$topic,
                    "payload"=>'SEARCH '.$topic
                ]
            ]
            ]
        ]];
    }

    function sendTrendResults($searchResults) {
        $this->send("Here are the top ".count($searchResults['trends'])." currently trending topics, ".$this->user->getFirstName().":");  
        $index = 1;
        foreach ($searchResults['trends'] as $trend) {
            $this->sendAction(SenderAction::typingOn);
            $this->send($this->getButtonResponseTemplate($index, $trend));
            $index++;
        }
    }

    function getResponseTemplate() {
        $template = ["attachment"=>[
            "type"=>"template",
            "payload"=>[
                "template_type"=>"list",
                "top_element_style"=>"large",
                "elements"=>array()
            ]
        ]];
        return $template;
    }

    function getSearchTimeTemplate($pageNum, $keyword, $total, $time) {
        $template = ["attachment"=>[
            "type"=>"template",
            "payload"=>[
                "template_type"=>"button",
                "text"=>"Searching took ".$time." ms, ".$this->user->getFirstName().".",
            "buttons"=>array()
            ]
            ]
        ];
        if ($pageNum > 1) {
            $template["attachment"]["payload"]["buttons"][] = [
                "type"=>'postback',
                "title"=>'Previous Results',
                "payload"=>$this->command.' '.($pageNum - 1).'~!@#'.$keyword
            ];
        }
        if (($total - ($pageNum * 4)) > 0) {
            $template["attachment"]["payload"]["buttons"][] = [
                "type"=>'postback',
                "title"=>'Next Results',
                "payload"=>$this->command.' '.($pageNum + 1).'~!@#'.$keyword
            ];
        }
        return $template;
    }
    
    function sendSearchResults($pageNum, $keyword, $searchResults) {
        $this->send("Displaying ".(($keyword == '') ? "trending " : "").($this->isSearch() ? 'topics' : 'news articles')." ".((($pageNum - 1) * 4) + 1)." to ".((($pageNum - 1) * 4) + count($searchResults['results']))." of ".$searchResults['count']." (Page ".$pageNum." of ".($searchResults['count'] / 4).")".(($keyword == '') ? ":" : ", about \"".$keyword."\":"));  
        $this->sendAction(SenderAction::typingOn);
        $responseTemplate = $this->getResponseTemplate();
        foreach ($searchResults['results'] as &$result) {
            $responseTemplate["attachment"]["payload"]["elements"][] = [
                "title"=>$result['title'],
                "image_url"=>($result['iurl'] == '') ? 'http://cadeuc.com/wp-content/themes/musen/img/no-image.png' : $result['iurl'],
                "subtitle"=>$result['kwic'],
                "default_action"=>[
                    "type"=>"web_url",
                    "url"=>$result['url']
                ]
            ];
        }
        if (count($searchResults['results']) == 1) {
            $responseTemplate["attachment"]["payload"]["template_type"] = "generic";
        }
        $this->send($responseTemplate);
        $this->sendAction(SenderAction::typingOn);
        $this->send($this->getSearchTimeTemplate($pageNum, $keyword, $searchResults['count'], $searchResults['time']));
    }
}