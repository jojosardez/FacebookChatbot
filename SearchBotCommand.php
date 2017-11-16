<?php

/**
 * Class SearchBotCommand
 * This class returns top trending topics or web and news articles search results based on the given keyword.
 *
 * Usage (for trending topics):
 *  TRENDING
 *  TREND
 * 
 * Usage (for web search):
 *  SEARCH | FIND | LOOK | QUERY
 *  SEARCH | FIND | LOOK | QUERY <keyword>
 *
 * Usage (for news articles):
 *  NEWS | NEW
 *  NEWS | NEW <keyword>
 *
 * @author: Angelito Sardez, Jr.
 * @date: 16/11/2017
 */
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
        return json_decode(file_get_contents('http://www.faroo.com/api?q='.($this->isTrending() ? '' : urlencode($keyword)).'&start='.$pageNum.'&length='.($this->isTrending() ? '10' : '8').'&l=en&src='.$this->getApiSrc($keyword).'&f=json', false, $headerContext), true);
    }

    function getApiSrc($keyword) {
        if ($this->isTrending()) {
            return "trends";
        }
        else {
            return $this->isSearch() ? 'web' : 'news';
        }
    }

    function getListTemplate() {
        $template = ["attachment"=>[
            "type"=>"template",
            "payload"=>[
                "template_type"=>"list",
                "top_element_style"=>"compact",
                "elements"=>array()
            ]
        ]];
        return $template;
    }

    function sendTrendResults($searchResults) {
        $this->send("Here are the top ".count($searchResults['trends'])." currently trending topics, ".$this->user->getFirstName().":");  
        $index = 1;
        $responseTemplate = $this->getListTemplate();
        $trendCount = 0;
        foreach ($searchResults['trends'] as $trend) {
            $responseTemplate["attachment"]["payload"]["elements"][] = [
                "title"=>$index.'. '.$trend,
                "buttons"=>[
                    [
                        "type"=>'postback',
                        "title"=>'View Articles',
                        "payload"=>'SEARCH '.$trend
                    ]
                ]
                ];
            $trendCount++;
            if ($trendCount == 4) {
                $this->send($responseTemplate);
                $responseTemplate = $this->getListTemplate();
                $trendCount = 0;
                $this->sendAction(SenderAction::typingOn);
            }
            $index++;
        }
        if ($trendCount == 1) {
            $responseTemplate["attachment"]["payload"]["template_type"] = "generic";
            unset($responseTemplate["attachment"]["payload"]["top_element_style"]);
        }
        if ($trendCount > 0) {
            $this->send($responseTemplate);
        }
    }

    function getResponseTemplate() {
        $template = ["attachment"=>[
            "type"=>"template",
            "payload"=>[
                "template_type"=>"generic",
                "elements"=>array()
            ]
        ]];
        return $template;
    }
    
    function getSearchTerm() {
        return $this->isSearch() ? 'topics' : 'news articles';
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
        return "Set ".$pageNum." of ".(round($totalCount / 8));
    }

    function sendSearchResults($pageNum, $keyword, $searchResults) {
        $this->send("Displaying ".(($keyword == '') ? "trending " : "").
            $this->getSearchTerm()." ".
            $this->getResultsCountRatioTerm($pageNum, count($searchResults['results']), $searchResults['count']).
            " (".$this->getSetRatioTerm($pageNum, $searchResults['count']).")".
            (($keyword == '') ? ":" : ", about \"".$keyword."\":"));  
        $this->sendAction(SenderAction::typingOn);
        $responseTemplate = $this->getResponseTemplate();
        
        if ($pageNum > 1) {
            $responseTemplate["attachment"]["payload"]["elements"][] = [
                "title"=>'See previous results',
                "image_url"=>'https://is238-group5.cf/bot/images/BackArrow.jpg',
                "subtitle"=>'Display the previous or first set of '.$this->getSearchTerm().'.',
                "buttons"=>[
                    [
                        "type"=>'postback',
                        "title"=>'Previous Results',
                        "payload"=>$this->command.' '.($pageNum - 1).'~!@#'.$keyword
                    ],
                    [
                        "type"=>'postback',
                        "title"=>'First Results',
                        "payload"=>$this->command.' 1~!@#'.$keyword
                    ]
                ]
            ];
        }

        $threshold = $this->getCountTo($pageNum, count($searchResults['results']), $searchResults['count']) - $this->getCountFrom($pageNum) + 1;
        $currentCount = 0;
        foreach ($searchResults['results'] as &$result) {
            $responseTemplate["attachment"]["payload"]["elements"][] = [
                "title"=>$result['title'],
                "image_url"=>($result['iurl'] == '') ? 'https://is238-group5.cf/bot/images/NoImageAvailable.jpg' : $result['iurl'],
                "subtitle"=>$result['kwic'],
                "default_action"=>[
                    "type"=>"web_url",
                    "url"=>$result['url']
                ],
                "buttons"=>[
                    [
                        "type"=>'web_url',
                        "url"=>$result['url'],
                        "title"=>$this->isSearch() ? "View Page" : "Read Article"
                    ]
                ]
            ];
            $currentCount++;
            if ($currentCount == $threshold) {
                break;
            }
        }

        if (($searchResults['count'] - ($pageNum * 8)) > 0) {
            $responseTemplate["attachment"]["payload"]["elements"][] = [
                "title"=>'See next results',
                "image_url"=>'https://is238-group5.cf/bot/images/NextArrow.jpg',
                "subtitle"=>'Display the next or last set of '.$this->getSearchTerm().'.',
                "buttons"=>[
                    [
                        "type"=>'postback',
                        "title"=>'Next Results',
                        "payload"=>$this->command.' '.($pageNum + 1).'~!@#'.$keyword
                    ],
                    [
                        "type"=>'postback',
                        "title"=>'Last Results',
                        "payload"=>$this->command.' '.(round($searchResults['count'] / 8)).'~!@#'.$keyword
                    ]
                ]
            ];
        }

        $this->send($responseTemplate);
    }
}