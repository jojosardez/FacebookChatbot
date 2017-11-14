<?php

/**
 * Class ImdbBotCommand
 * This class queries IMDB API to search and find movie titles
 *
 * usage:
 *  imdb movie
 *  imdb title movie
 *
 *  @author: Karez Bartolo
 *  @date: 15/11/2017
 */
class ImdbBotCommand extends BotCommand {
    public function __construct($sender, $user) {
        parent::__construct("IMDB", $sender, $user);
    }

    protected function executeCommand($parameter) {

        $first = strtok($parameter, " ");
        if ($first == "title") {
            $parameter = explode("title ", $parameter);

            $this->getMovieDetails(urlencode($parameter[1]));
        } else {
            $this->searchMovieTitle(urlencode($parameter));
        }
    }


    protected function searchMovieTitle($title) {
        $IMDB_URL = 'http://www.omdbapi.com/?s='.$title.'&apikey=86030ddd';

        if (!empty($title)) {
            $response = $this->curl_invoke($IMDB_URL);
            $imdb = json_decode($response, true);

            if (!empty($imdb) && $imdb["Response"] == "True") {

                $matchArray = $imdb["Search"];

                $size = sizeof($matchArray);

                $this->send("Hey " . $this->user->getFirstName() . ", there are " . $size . " matched result(s). But displaying only the most relevant.");

                $results = ["attachment" => [
                    "type" => "template",
                    "payload" => [
                        "template_type" => "list",
                        "top_element_style" => "compact",
                        "elements" => array(),
                        "buttons" => [
                            [
                                "type" => 'web_url',
                                "url" => 'http://www.imdb.com/find?ref_=nv_sr_fn&q=' . $title . '&s=all',
                                "title" => "View All"
                            ]
                        ]
                    ]
                ]];

                if ($size == 1) {
                    $item = $matchArray[0];

                    $iTitle = $item["Title"];
                    $iYear = $item["Year"];
                    $iMovieid = $item["imdbID"];
                    $iPoster = $item["Poster"];
                    $imdburl = 'www.imdb.com/title/' . $iMovieid;

                    $this->send(["attachment" => [
                        "type" => "template",
                        "payload" => [
                            "template_type" => "generic",
                            "elements" => [
                                [
                                    "title" => $iTitle . " (" . $iYear . ")",
                                    "image_url" => $iPoster,
                                    "buttons" => [
                                        [
                                            "type" => "web_url",
                                            "url" => $imdburl,
                                            "title" => "View More"
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]]);
                } else {
                    $cnt = 0;

                    while ($cnt < 4 && $cnt < $size) {
                        $item = $matchArray[$cnt];
                        $iTitle = $item["Title"];
                        $iYear = $item["Year"];
                        $iMovieid = $item["imdbID"];
                        $iPoster = $item["Poster"];

                        if ($iPoster == "N/A") {
                            $iPoster = "https://www.joomlatools.com/images/developer/ui/placeholder-16-9.png.pagespeed.ce.gT4LjHxoYL.png";
                        }

                        $imdburl = 'www.imdb.com/title/' . $iMovieid;

                        $results["attachment"]["payload"]["elements"][] = [
                            "title" => $iTitle . " (" . $iYear . ")",
                            "image_url" => $iPoster,
                            "buttons" => [
                                [
                                    "type" => 'postback',
                                    "title" => "More details",
                                    "payload" => "imdb title " . $iTitle
                                ]
                            ]
                        ];
                        $cnt++;
                    }

                    $this->send($results);
                }

            } else {
                $this->send("Sorry your movie " . $title . " is not found");
            }


        } else {
            $this->send("Did you forget to provide the movie title?");
        }

    }

    protected function getMovieDetails($title)
    {

        $IMDB_URL = 'http://www.omdbapi.com/?t=' . $title . '&apikey=86030ddd';

        if (!empty($title)) {
            $response = $this->curl_invoke($IMDB_URL);

            $imdb = json_decode($response, true);

            if (!empty($imdb) && $imdb["Response"] == "True") {
                $title = $imdb["Title"] . "(" . $imdb["Year"] . ")";
                $moviedetails = $imdb["Plot"];
                $poster = $imdb["Poster"];
                if ($poster == "N/A") {
                    $poster = "https://www.joomlatools.com/images/developer/ui/placeholder-16-9.png.pagespeed.ce.gT4LjHxoYL.png";
                }
                $imdburl = 'www.imdb.com/title/' . $imdb["imdbID"];

                $this->send(["attachment" => [
                    "type" => "template",
                    "payload" => [
                        "template_type" => "generic",
                        "elements" => [
                            [
                                "title" => $title,
                                "image_url" => $poster,
                                "subtitle" => $moviedetails,
                                "buttons" => [
                                    [
                                        "type" => "web_url",
                                        "url" => $imdburl,
                                        "title" => "View on IMDB"
                                    ]/*,
                                    [
                                        "type" => "postback",
                                        "url" => "This is the visible text",
                                        "payload" => "This is the value you get back"
                                    ]*/
                                ]
                            ]
                        ]
                    ]
                ]]);

                $releasedate = $imdb["Released"];
                $country = $imdb["Country"];
                $metascore = $imdb["Metascore"];
                $director = $imdb["Director"];
                $actors = $imdb["Actors"];

                $description = "This was released " . $releasedate . " from the " . $country . " with a metascore of " . $metascore . ". Directed by " . $director . " and starring " . $actors;

                $this->send($description);
            } else {
                $this->send("Sorry your movie " . $title . " is not found");
            }

        } else {
            $this->send("Did you forget to provide the movie title?");
        }
    }

    protected function curl_invoke($url) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
        $response = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        // Handle failed query
        if ($httpcode != 200) {
            $this->send("IMDB is unavailable. Please try again, ". $this->user->getFirstName());
            die();
        }

        return $response;
    }

}