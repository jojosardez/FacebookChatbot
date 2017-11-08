<?php

class WeatherBotCommand extends BotCommand {
    public function __construct() {
        parent::__construct("WEATHER");
    }

    protected function executeCommand($parameter) {
        $acResult = json_decode(file_get_contents('http://autocomplete.wunderground.com/aq?query='.urlencode($parameter)), true);
        $zmwCode = $acResult['RESULTS'][0]['zmw'];
        $condResult = json_decode(file_get_contents('http://api.wunderground.com/api/ec23707d4592d0cb/conditions/q/zmw:'.urlencode($zmwCode.'.json')), true);
        return ["attachment"=>[
            "type"=>"template",
            "payload"=>[
              "template_type"=>"generic",
              "elements"=>[
                [
                  "title"=>'Current weather conditions in '.$condResult['current_observation']['display_location']['full'],
                  "image_url"=>$condResult['current_observation']['icon_url'],
                  "subtitle"=>$condResult['current_observation']['weather'].' at '.$condResult['current_observation']['temperature_string'].'. '.$condResult['current_observation']['observation_time'].'.',
                  "buttons"=>[
                    [
                      "type"=>'web_url',
                      "url"=>'http://www.wunderground.com/q/zmw:'.$zmwCode,
                      "title"=>"View Full Forecast"
                    ]
                  ]
                ]
              ]
            ]
          ]];
    }
}