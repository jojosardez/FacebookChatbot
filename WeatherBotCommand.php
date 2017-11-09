<?php

class WeatherBotCommand extends BotCommand
{
    public function __construct($sender, $user)
    {
        parent::__construct("WEATHER", $sender, $user);
    }

    protected function executeCommand($parameter)
    {
        if (trim($parameter) == "") {
            $this->send("Sorry ".$this->user->getFirstName().", you need to specify the location, whose weather condition and forecast you wanted to see. (e.g. WEATHER Manila PH)");
            return;
        }

        $acResult = json_decode(file_get_contents('http://autocomplete.wunderground.com/aq?query='.urlencode($parameter)), true);

        $zmwCode = "";
        if (!empty($acResult['RESULTS'])) {
            foreach ($acResult['RESULTS'] as &$location) {
                if (strtolower($location['tz']) != 'missing') {
                    $zmwCode = $location['zmw'];
                    break;
                }
            }
        } 

        if (empty($zmwCode)) {
          $this->send("No weather condition and forecast found for the location \"".$parameter."\", ".$this->user->getFirstName().". Please make sure that the place exists or be more specific in providing the location. (e.g. WEATHER Singapore, Singapore)");
        }
        else {
            $condResult = json_decode(file_get_contents('http://api.wunderground.com/api/ec23707d4592d0cb/conditions/q/zmw:'.urlencode($zmwCode.'.json')), true);
            $this->send(["attachment"=>[
                "type"=>"template",
                "payload"=>[
                    "template_type"=>"list",
                    "elements"=>[
                    [
                        "title"=>'Current weather condition in '.$condResult['current_observation']['display_location']['full'].':',
                        "image_url"=>$condResult['current_observation']['icon_url'],
                        "subtitle"=>$condResult['current_observation']['weather'].' at '.$condResult['current_observation']['temperature_string'].'. '.$condResult['current_observation']['observation_time'].'.'
                    ],
                    [
                        "title"=>'Precipitation forecast for '.$condResult['current_observation']['display_location']['full'].':',
                        "subtitle"=>'Today would be '.$condResult['current_observation']['precip_today_string'].', while '.$condResult['current_observation']['precip_1hr_string'].' in the next hour.'
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
            if (sizeof($acResult['RESULTS']) > 1) {
                $locations = "";
                foreach ($acResult['RESULTS'] as &$location) {
                    $locations = $locations.$location['name']." | ";
                }
                $this->send("Hey ".$this->user->getFirstName().", there were actually ".sizeof($acResult['RESULTS'])." locations that matched \"".$parameter."\". I just returned the most relevant match but you can be more specific by typing the name of the other locations completely. (e.g. WEATHER < ".rtrim($locations,"| ")." >)");
            }
        }
    }
}
