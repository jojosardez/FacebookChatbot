<?php

/**
* Class HelpBotCommand
* This class returns bot command usage information.
*
* Usage:
*  HELP
*  HELP <COMMAND>
*
* @author: Karez Bartolo
* @date: 11/11/2017
*/

    class HelpBotCommand extends BotCommand {
    public function __construct($sender, $user) {
        parent::__construct("HELP", $sender, $user);
    }

    protected function executeCommand($parameter) {

        $lowerParameter = strtolower($parameter);
        if (empty($parameter)) {
            $this->send("Hey ".$this->user->getFirstName().", there are a list of things you can ask me");
            $this->send("imdb, php, cinema, weather, phone, gender, recipe, pokedex, ip, history, trump, university, netflix");
            $this->send("Want to know more? Try help and the keyword you're looking for (i.e. help imdb)");
        } elseif($lowerParameter == "imdb") {
            $this->displayUsage("Wanna search for a movie? You can do so by typing the movie title (i.e. imdb pitch perfect 3)", "imdb pitch perfect 3");
        } elseif($lowerParameter == "php") {
            $this->displayUsage("Traveling abroad? Check out the exchange rate (i.e. php currency)","php aud");
        } elseif($lowerParameter == "cinema") {
            $this->displayUsage("Looking for cinemas? Type in a location and press enter (i.e. cinema greenbelt)","cinema greenbelt");
        }elseif($lowerParameter == "weather") {
            $this->displayUsage("Do you think you need umbrella today? Ask the weather (i.e. weather Sydney, Australia)","weather Sydney, Australia");
        }elseif($lowerParameter == "phone") {
            $this->displayUsage("Need to do phone number check? Easy! (i.e. phone phonenumber)","phone +61415232985");
        }elseif($lowerParameter == "gender") {
            $this->displayUsage("Not sure if it's a boy or girl? Gender it (i.e. gender minh)","gender minh");
        }elseif($lowerParameter == "recipe") {
            $this->displayUsage("Don't know what's for dinner? Search a recipe (i.e. recipe adobo)","recipe adobo");
        }elseif($lowerParameter == "pokedex") {
            $this->displayUsage("Catching wild pokemons? Ask Dexter (i.e. pokedex ditto)","pokedex ditto");
        }elseif($lowerParameter == "ip") {
            $this->displayUsage("Dodgy IP? Check some details (i.e. ip 74.125.224.72)","ip 74.125.224.72");
        }elseif($lowerParameter == "history") {
            $this->displayUsage("Get your history trivia today (i.e. history)","history");
        }elseif($lowerParameter == "trump") {
            $this->displayUsage("Feelin like Trump? He'll give some advice (i.e. trump)","trump");
        }elseif($lowerParameter == "university") {
            $this->displayUsage("Forgot your university website? I'll find for you (i.e. university university of the philippines","university university of the philippines");
        }elseif($lowerParameter == "netflix") {
            $this->displayUsage("Doing some binge watching today? Check out titles (i.e. netflix enchanted)","netflix enchanted");
        }



    }

    function displayUsage($text, $payload) {
        $this->send(["attachment" => [
            "type" => "template",
            "payload" => [
                "template_type" => "button",
                "text" => $text,
                "buttons" => [
                    [
                        "type" => 'postback',
                        "title" => $payload,
                        "payload" => $payload
                    ]
                ]
            ]
        ]]);
    }
}