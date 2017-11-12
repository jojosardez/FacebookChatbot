<?php

class CinemaBotCommand extends BotCommand {
    public function __construct($sender, $user) {
        parent::__construct("CINEMA", $sender, $user);
    }

    protected function executeCommand($parameter) {
        if (trim($parameter) == "") {
            $this->sendTextWithHelp("Sorry ".$this->user->getFirstName().", you need to specify the Ayala Malls Cinema name, whose viewing schedules you wanted to see. (e.g. CINEMA Glorietta 4).");
            return;
        }

        $theater = $this->quearyTheater($parameter);
        if (empty($theater)) {
            $this->send("No Ayala Malls Cinema found with the name \"".$parameter."\", ".$this->user->getFirstName().". Please make sure that the theater exists and it is an Ayala Malls Cinema. You can be more specific in providing its name as well. (e.g. \"CINEMA Greenbelt 1\" or \"CINEMA Greenbelt 3\" instead of just \"CINEMA Greenbelt\")");
        }
        else {
            $movies = $this->queryMovies($theater["theater_id"]);
            if (sizeof($movies) == 0) {
                $this->send("No movies are showing today in \"".$theater["name"]."\" Ayala Malls Cinema, ".$this->user->getFirstName().". You could try searching on other Ayala Malls Cinema.");
            }
            else {
                $this->sendMovieCinemas($theater, $movies);
            }
        }
    }

    function quearyTheater($name) {
        $theaters = json_decode(file_get_contents('https://api2.sureseats.com/api/theaters'), true);
        $name = strtolower(str_replace(' ', '-', str_replace('.', '', $name)));
        foreach ($theaters["result"] as &$theater) {
            if (strpos($theater['slug'], $name) !== false) {
                return $theater;
            }
        }
        return null;
    }

    function queryMovies($theaterId) {
        $allMovies = json_decode(file_get_contents('https://api2.sureseats.com/api/movies'), true);
        $movies = array();
        foreach ($allMovies["result"] as &$movie) {
            if (in_array($theaterId, $movie['theaters'])) {
                $movies[] = $movie;
            }
        }
        return $movies;
    }

    function getResponseTemplate($firstResult) {
        return ["attachment"=>[
            "type"=>"template",
            "payload"=>[
                "template_type"=>"list",
                "top_element_style"=>$firstResult ? "large" : "compact",
                "elements"=>array(),
            "buttons"=>[
                [
                    "type"=>'web_url',
                    "url"=>'https://www.sureseats.com/quick-cinema',
                    "title"=>"View All Cinemas"
                ]
            ]
            ]
        ]];
    }

    function sendMovieCinemas($theater, $movies) {
        $this->send("Displaying \"".$theater['name']."\" cinema schedules for today (".date('F j, Y').")...");
        $this->sendAction(SenderAction::typingOn);
        $sent = false;
        $responseTemplate = $this->getResponseTemplate(true);
        $cinemaCount = 0;
        foreach($movies as &$movie) {
            foreach ($movie['schedules'] as $schedule) { 
                if ($schedule['date_id'] == date('Y-m-d')) {
                    foreach ($schedule['theaters'] as &$schedTheater) {
                        if ($schedTheater['theater_id'] == $theater['theater_id']) {
                            foreach ($schedTheater['cinemas'] as &$cinema) {
                                $showingTimes = '';
                                foreach ($cinema['times'] as &$cinemaTime) {
                                    $showingTimes = $showingTimes.$cinemaTime['value']." | ";
                                }                                
                                $responseTemplate["attachment"]["payload"]["elements"][] = [
                                    "title"=>$cinema['cinema_name'].": ".$movie['title'],
                                    "image_url"=>$movie['poster'],
                                    "subtitle"=>"Showtimes: ".rtrim($showingTimes," | ")
                                ];
                                $cinemaCount++;
                                if ($cinemaCount == 4) {
                                    $this->send($responseTemplate);
                                    $sent = true;
                                    $responseTemplate = $this->getResponseTemplate(false);
                                    $cinemaCount = 0;
                                    $this->sendAction(SenderAction::typingOn);
                                }
                            }
                            break 2;                    
                        }
                    }
                }
            }
        }
        if ($cinemaCount == 1) {
            $responseTemplate["attachment"]["payload"]["template_type"] = "generic";
            $responseTemplate["attachment"]["payload"]["elements"][0]["buttons"] = [
                [
                    "type"=>'web_url',
                    "url"=>'https://www.sureseats.com/quick-cinema',
                    "title"=>"View All Cinemas"
                ]
            ];
            unset($responseTemplate["attachment"]["payload"]["top_element_style"]);
            unset($responseTemplate["attachment"]["payload"]["buttons"]);
        }
        if ($cinemaCount > 0) {
            $this->send($responseTemplate);
            $sent = true;
        }
        if (!$sent) {
            $this->send("Oops! It seems like there are no movies showing in any of \"".$theater['name']."\" cinemas today, ".$this->user->getFirstName().". You could try searching on other Ayala Malls Cinema.");
        }
    }
}