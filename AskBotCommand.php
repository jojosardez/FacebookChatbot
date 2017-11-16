<?php

/**
 * Class AskBotCommand
 * This class randomnly answers using Magic_8-Ball
 *
 * usage:
 *  ask sentence
 *
 *
 *  @author: Karez Bartolo
 *  @date: 15/11/2017
 */
class AskBotCommand extends BotCommand {
    public function __construct($sender, $user) {
        parent::__construct("ECHO", $sender, $user);
    }

    protected function executeCommand($parameter) {
        $MAGIC_URL = "https://8ball.delegator.com/magic/JSON/";

        if (!empty($parameter)) {
            $talkback = $this->curl_invoke($MAGIC_URL);

            $answer = $talkback["magic"]["answer"];
            $type = $talkback["magic"]["type"];

            if ($type == "Affirmative") {
                $emoji = ":3";
            } elseif ($type = "Contrary") {
                $emoji = "[[notbaad]]";
            } else {
                $emoji = ":|]";
            }
            $this->send("Hey ".$this->user->getFirstName().", ".$answer." ".$emoji);
        } else {
            $this->send("Maybe you forgot your pants today? Ask a question! ".$this->user->getFirstName()." :poop:");
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