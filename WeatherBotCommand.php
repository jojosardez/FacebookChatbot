<?php

class WeatherBotCommand extends BotCommand {
    public function __construct() {
        parent::__construct("WEATHER");
    }

    protected function executeCommand($parameter) {
        $acResult = json_decode(file_get_contents('http://autocomplete.wunderground.com/aq?query='.urlencode($parameter)), true);
        $condResult = json_decode(file_get_contents('http://api.wunderground.com/api/ec23707d4592d0cb/conditions/q/zmw:'.urlencode($acResult['RESULTS'][0]['zmw'].'.json')), true);
        return 'Displaying current weather conditions in '.$condResult['current_observation']['display_location']['full'];
    }
}