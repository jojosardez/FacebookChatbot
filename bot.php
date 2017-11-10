<?php
$files = glob(__DIR__ . '/*.php');

foreach ($files as $file) {
    require_once $file;   
}

// parameters
$hubVerifyToken = 'Is238Group5Chatbot';
$accessToken =   "EAAKiQ7FkXIgBAOiuiAJyCw2r7Ti5qCEMTz9eXLE16yBrfxZBijHtH7ZBDZCRhPYw8TvfanbHBqIG0qATTOXAGYWTrVfpYYoeZCV6nU1ZBtgPjYZC1rvk1LOpbHOZAJlGw9c5XQtrujj5cRg4kMZBI0fgXgWGlYiRfjZAE97kUoZAQXrWjX0zeDUSq9"; 
// check token at setup
if ($_REQUEST['hub_verify_token'] === $hubVerifyToken) {
  echo $_REQUEST['hub_challenge'];
  exit;
}
// execute bot command
$input = json_decode(file_get_contents('php://input'), true);
$senderId = $input['entry'][0]['messaging'][0]['sender']['id'];
$messageText = $input['entry'][0]['messaging'][0]['message']['text'];
$command = trim(explode(" ", $messageText)[0]);
$parameter =  trim(substr($messageText, strlen($command)));
// get userDetails
$userDetails = json_decode(file_get_contents('https://graph.facebook.com/v2.6/'.$senderId.'?fields=first_name,last_name,profile_pic,locale,timezone,gender&access_token='.$accessToken), true);
$user = new User($userDetails);
// create sender
$sender = new Sender($senderId, $accessToken);
// resolve bot command
try {
  $botCommand = BotCommandFactory::create($command, $sender, $user);
  $botCommand->execute($parameter);
}
catch  (Exception $e) {
  $sender->send("Oops! I encountered an exception: \"".$e->getMessage()."\". Sorry about that, ".$user->getFirstName().". Please try again.");
}