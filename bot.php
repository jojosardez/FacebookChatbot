<?php

/**
 * Script Bot.php
 * This is the entry point of the Chatbot script.
 *
 * @author: Angelito Sardez, Jr.
 * @date: 13/11/2017
 */

$files = glob(__DIR__ . '/*.php');

foreach ($files as $file) {
    require_once $file;   
}

// parameters
$hubVerifyToken = 'Is238Group5Chatbot';
$accessToken =   "EAAKiQ7FkXIgBAGMwBMNT5gkSHdH0u5jS0mlrM1Tj5ElOnMOSPxryIgsLK2t1lV1KcIkpDpcklIIJfBLsfTcbi02BTarZBnUBEoKxMAHPDWqkAuSqcWSb7xjkpc9n5aTOzgP20c3Y1liZB8Wu2tN4OQBMkb4ObGKZCfWmhEmY21DxJDJZBrNu"; 
// check token at setup
//if ($_REQUEST['hub_verify_token'] === $hubVerifyToken) {
//  echo $_REQUEST['hub_challenge'];
//  exit;
//}
// execute bot command
$input = json_decode(file_get_contents('php://input'), true);
$senderId = $input['entry'][0]['messaging'][0]['sender']['id'];
// create sender
$sender = new Sender($senderId, $accessToken);
$sender->sendAction(SenderAction::typingOn);
// extract message
$messageText = $input['entry'][0]['messaging'][0]['message']['text'];
if (trim($messageText) == "") {
  $messageText = $input['entry'][0]['messaging'][0]['postback']['payload'];
}
$command = trim(explode(" ", $messageText)[0]);
$parameter =  trim(substr($messageText, strlen($command)));
// get userDetails
$userDetails = json_decode(file_get_contents('https://graph.facebook.com/v2.11/'.$senderId.'?fields=first_name,last_name,profile_pic,locale,timezone,gender&access_token='.$accessToken), true);
$user = new User($userDetails);
// resolve bot command
try {
  $botCommand = BotCommandFactory::create($command, $sender, $user);
  $botCommand->execute($parameter);
  $sender->sendAction(SenderAction::typingOff);
}
catch  (Exception $e) {
  $sender->send("Oops! I encountered an exception: \"".$e->getMessage()."\". Sorry about that, ".$user->getFirstName().". Please try again.");
}
$sender->sendAction(SenderAction::typingOff);