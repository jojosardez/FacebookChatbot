<?php

/**
 * Class WeatherBotCommand
 * This class returns the current weather condition and forecast for the given location.
 *
 * Usage:
 *  WEATHER <location>
 *
 * @author: Angelito Sardez, Jr.
 * @date: 13/11/2017
 */
class WeatherBotCommand extends BotCommand
{
    public function __construct($sender, $user)
    {
        parent::__construct("WEATHER", $sender, $user);
    }

    protected function executeCommand($parameter)
    {
        if (trim($parameter) == "") {
            $this->sendTextWithHelp("Sorry ".$this->user->getFirstName().", you need to specify the location, whose weather condition and forecast you wanted to see. (e.g. WEATHER Manila PH)");
            return;
        }

        $locations = $this->queryLocation($parameter);
        $validLocationsCount = $this->getValidLocationsCount($locations);

        if ($validLocationsCount == 0) {
          $this->send("No weather condition and forecast found for the location \"".$parameter."\", ".$this->user->getFirstName().". Please make sure that the place exists or be more specific in providing the location. (e.g. WEATHER Singapore, Singapore)");
        }
        else {
            if ($validLocationsCount > 1) {
                $this->sendMultipleLocationsMessage($locations, $parameter);                
            }
            else {
                $zmwCode = $this->getZmwCode($locations);
                $weatherCondition = $this->getWeatherCondition($zmwCode);
                $this->sendWeatherCondition($zmwCode, $weatherCondition);
            }
        }
    }

    function queryLocation($location) {
        return json_decode(file_get_contents('http://autocomplete.wunderground.com/aq?query='.urlencode($location)), true);
    }

    function getValidLocationsCount($locations) {
        $count = 0;
        if (!empty($locations['RESULTS'])) {
            foreach ($locations['RESULTS'] as &$place) {
                if (strtolower($place['tz']) != 'missing') {
                    $count++;
                }
            }
        }
        return $count;
    }

    function getZmwCode($locations) {
        $zmwCode = "";
        if (!empty($locations['RESULTS'])) {
            foreach ($locations['RESULTS'] as &$place) {
                if (strtolower($place['tz']) != 'missing') {
                    $zmwCode = $place['zmw'];
                    break;
                }
            }
        }
        return $zmwCode;
    }

    function getWeatherCondition($zmwCode) {
        return json_decode(file_get_contents('http://api.wunderground.com/api/ec23707d4592d0cb/conditions/q/zmw:'.urlencode($zmwCode.'.json')), true);
    }

    function sendWeatherCondition($zmwCode, $weatherCondition) {
        $this->send(["attachment"=>[
            "type"=>"template",
            "payload"=>[
                "template_type"=>"list",
                "elements"=>[
                [
                    "title"=>'Current weather condition in '.$weatherCondition['current_observation']['display_location']['full'].':',
                    "image_url"=>$weatherCondition['current_observation']['icon_url'],
                    "subtitle"=>$weatherCondition['current_observation']['weather'].' at '.$weatherCondition['current_observation']['temperature_string'].'. '.$weatherCondition['current_observation']['observation_time'].'.'
                ],
                [
                    "title"=>'Precipitation forecast for '.$weatherCondition['current_observation']['display_location']['full'].':',
                    "subtitle"=>'Today would be '.$weatherCondition['current_observation']['precip_today_string'].', while '.$weatherCondition['current_observation']['precip_1hr_string'].' in the next hour.'
                ]
            ],
            "buttons"=>[
                [
                    "type"=>'web_url',
                    "url"=>'http://www.wunderground.com/q/zmw:'.$zmwCode,
                    "title"=>"View Full Forecast"
                ]
            ]
            ]
            ]]);
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

    function sendMultipleLocationsMessage($locations, $location) {
        $this->send("Hey ".$this->user->getFirstName().", there are more than 1 location that matched \"".$location."\". Which one are you looking for:");
        $this->sendAction(SenderAction::typingOn);
        $responseTemplate = $this->getListTemplate();
        $firstMatch = '';
        $locationCount = 0;
        foreach ($locations['RESULTS'] as &$place) {
            if (strtolower($place['tz']) != 'missing') {
                if ($firstMatch == '') {
                    $firstMatch = $place['name'];
                }
                $responseTemplate["attachment"]["payload"]["elements"][] = [
                    "title"=>$place['name'],
                    "buttons"=>[
                        [
                            "type"=>'postback',
                            "title"=>'View Details',
                            "payload"=>'WEATHER '.$place['name']
                        ]
                    ]
                    ];
                $locationCount++;
                if ($locationCount == 4) {
                    $this->send($responseTemplate);
                    $responseTemplate = $this->getListTemplate();
                    $locationCount = 0;
                    $this->sendAction(SenderAction::typingOn);
                }
            }
        }
        if ($locationCount == 1) {
            $responseTemplate["attachment"]["payload"]["template_type"] = "generic";
            unset($responseTemplate["attachment"]["payload"]["top_element_style"]);
        }
        if ($locationCount > 0) {
            $this->send($responseTemplate);
        }
        $this->send("Next time you can skip this by providing the location completely e.g. \"WEATHER ".$firstMatch."\".");
    }
}
