<?php

class PokedexBotCommand extends BotCommand {
    public function __construct($sender, $user) {
        parent::__construct("POKEDEX", $sender, $user);
    }

    protected function executeCommand($parameter) {

        $this->getPokeDetails($parameter);

    }


    protected function getPokeDetails($pokemon) {

        $GET_POKEMON_DETAILS_URL = 'https://pokeapi.co/api/v2/pokemon/'.$pokemon;

        if(!empty($pokemon)){

            $response = $this->curl_invoke($GET_POKEMON_DETAILS_URL);

            if (!empty($response)) {
                $dex = json_decode($response, true);
                $pokename = ucfirst($dex['name']);
                $sprite = $dex['sprites']['front_default'];
                $pokeid = $dex['id'];

                //
                $abilities = $dex['abilities'];
                $poketype = $dex['types'][0]['type']['name'];
                $weight = $dex['weight'];
                $height = $dex['height']/10;

                $ability = null;
                foreach ($abilities as $entry) {
                    $ability .= $entry['ability']['name'].', ';
                }

                $ability = rtrim($ability, ', ');
                $ability = preg_replace('/,([^,]*)$/', ' and $1', $ability);
                $ability = preg_replace('/\s\s/', ' ', $ability);

                $description = $pokename." is a ". $poketype." type pokemon which weighs ".$weight."g and has a height of approximately ".$height."m. It has ".sizeof($abilities)." abilities namely ".$ability.'.';

                $this->send(["attachment"=>[
                    "type"=>"template",
                    "payload"=>[
                        "template_type"=>"generic",
                        "elements"=>[
                            [
                                "title"=> $pokeid.": ".$pokename,
                                "image_url"=> $sprite,
                                "buttons" => [[
                                    "type"=>"web_url",
                                    "url"=> 'https://pokemondb.net/pokedex/'.$pokename,
                                    "title"=> "View More"
                                ]]
                            ]
                        ]
                    ]
                ]]);

                $this->send($description);

            } else {
                $this->send("Sorry no details found for ".$pokemon);
            }

        } else {
            $this->send("No pokemon to search");
        }

    }

    protected function curl_invoke($url) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        $response = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        // Handle failed query
        if ($httpcode != 200) {
            $this->send("Pokedex has an internal problem ERRCODE: ".$httpcode);
            die();
        }

        return $response;
    }

}
