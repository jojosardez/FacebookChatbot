<?php

class ImdbBotCommand extends BotCommand {
    public function __construct($sender, $user) {
        parent::__construct("IMDB", $sender, $user);
    }

    protected function executeCommand($parameter) {
        $this->getMovieDetails(urlencode($parameter));
    }

    protected function getMovieDetails($title) {

        $IMDB_URL = 'http://www.omdbapi.com/?t='.$title.'&apikey=86030ddd';

        if(!empty($title)) {
            $ch = curl_init($IMDB_URL);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
            $response = curl_exec($ch);
            $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            // Handle failed query
            if ($httpcode != 200) {
                $this->send("Theatres are closed");
                die();
            }

            $imdb = json_decode($response, true);

            if (!empty($imdb) && $imdb["Response"] == "True") {
                $title = $imdb["Title"]."(".$imdb["Year"].")";
                $moviedetails = $imdb["Plot"];
                $poster = $imdb["Poster"];
                $imdburl = 'www.imdb.com/title/'.$imdb["imdbID"];

                $this->send(["attachment"=>[
                    "type"=>"template",
                    "payload"=>[
                        "template_type"=>"generic",
                        "elements"=>[
                            [
                                "title"=>$title,
                                "image_url"=>$poster,
                                "subtitle"=>$moviedetails,
                                "buttons" => [[
                                    "type"=>"web_url",
                                    "url"=> $imdburl,
                                    "title"=> "View More"
                                ]]
                            ]
                        ]
                    ]
                ]]);
            } else {
                $this->send("Sorry your movie ".$title." is not found");
            }

        } else {
            $this->send("Did you forget to provide the movie title?");
        }


    }

}