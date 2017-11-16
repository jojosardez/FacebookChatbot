<?php

/**
 * Class CinemaBotCommand
 * This class returns screening times for a given Ayala Malls Cinema.
 *
 * Usage:
 *  CINEMA <Ayala Malls Cinema Name>
 *
 * @author: Angelito Sardez, Jr.
 * @date: 16/11/2017
 */
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

    function getResponseTemplate() {
        $template = ["attachment"=>[
            "type"=>"template",
            "payload"=>[
                "template_type"=>"generic",
                "elements"=>array()
            ]
        ]];
        return $template;
    }

    function sendMovieCinemas($theater, $movies) {
        date_default_timezone_set('Asia/Manila');
        $this->send("Displaying ".$theater['name']." cinema schedule for today (".date('F j, Y')."):");
        $this->sendAction(SenderAction::typingOn);
        $responseTemplate = $this->getResponseTemplate();
        $cinemaCount = 0;
        $totalCinemaCount = 0;
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
                                    "image_url"=>($movie['poster'] == '') ? 'https://is238-group5.cf/bot/images/NoImageAvailable.jpg' : $movie['poster'],
                                    "subtitle"=>"Showtimes: ".rtrim($showingTimes," | ")
                                ];
                                $cinemaCount++;
                                $totalCinemaCount++;
                                if ($cinemaCount == 10) {
                                    $this->send($responseTemplate);
                                    $responseTemplate = $this->getResponseTemplate();
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
        if ($totalCinemaCount == 0) {
            $this->send("Oops! It seems like there are no movies showing in any of \"".$theater['name']."\" cinemas today, ".$this->user->getFirstName().". You could try searching on other Ayala Malls Cinema.");
        }
        else {
            $responseTemplate["attachment"]["payload"]["elements"][] = [
                "title"=>'View All Cinemas',
                "image_url"=>'https://is238-group5.cf/bot/images/Cinema.jpg',
                "subtitle"=>'Check all cinema schedules, reserve seats, and buy tickets online!',
                "default_action"=>[
                    "type"=>'web_url',
                    "url"=>'https://www.sureseats.com/quick-cinema'
                ],
                "buttons"=>[
                    [
                        "type"=>'web_url',
                        "url"=>'https://www.sureseats.com/quick-cinema',
                        "title"=>'View All Cinemas'
                    ]
                ]
            ];
            $this->send($responseTemplate);
        }
    }
}