<?php

class WeatherBotCommand extends BotCommand {
    public function __construct() {
        parent::__construct("WEATHER");
    }

    protected function executeCommand($parameter) {
        $acResult = json_decode(file_get_contents('http://autocomplete.wunderground.com/aq?query='.urlencode($parameter)), true);
        $condResult = json_decode(file_get_contents('http://api.wunderground.com/api/ec23707d4592d0cb/conditions/q/zmw:'.urlencode($acResult['RESULTS'][0]['zmw'].'.json')), true);
        return ["attachment"=>[
            "type"=>"template",
            "payload"=>[
              "template_type"=>"generic",
              "elements"=>[
                [
                  "title"=>'Displaying current weather conditions in '.$condResult['current_observation']['display_location']['full'],
                  "item_url"=>$condResult['current_observation']['forecast_url'],
                  "image_url"=>$condResult['current_observation']['icon_url'],
                  "subtitle"=>'Weather: '.$condResult['current_observation']['weather'].', Temperature: '.$condResult['current_observation']['temperature_string'].', Humidity: '.$condResult['current_observation']['relative_humidity'].', '.$condResult['current_observation']['observation_time'].'.'
                ]
              ]
            ]
          ]];
    }
}