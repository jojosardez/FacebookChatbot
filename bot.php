<?php
$files = glob(__DIR__ . '/*.php');

foreach ($files as $file) {
    require_once $file;   
}

// parameters
$hubVerifyToken = 'Is238Group5Chatbot';
$accessToken =   "EAAKiQ7FkXIgBAACmoZBPAaZBvPxj7cblkIw9xEoOmXc6z41OSMSbMQsZAAcZB5nvucMrZAfzpc2cWPl6sgLuPpy4mPY7J1iZB4kDdq1Y9e7MOLrTSumhp1DhWU9Iwz6atAD5jA8TA5vTLZBT2dZAdnp8bXvA3B01NZCL4slzis3kMOLzZBoMW6gqN4"; 
// check token at setup
if ($_REQUEST['hub_verify_token'] === $hubVerifyToken) {
  echo $_REQUEST['hub_challenge'];
  exit;
}
// handle bot's anwser
$input = json_decode(file_get_contents('php://input'), true);
$senderId = $input['entry'][0]['messaging'][0]['sender']['id'];
$messageText = $input['entry'][0]['messaging'][0]['message']['text'];
$command = trim(explode(" ", $messageText)[0]);
$parameter =  trim(substr($messageText, strlen($command)));
// resolve bot command
$botCommand = BotCommandFactory::create($command);
$answer = $botCommand->execute($parameter);
//send message to facebook bot
if (is_string($answer))
{
    $response = [
        'recipient' => [ 'id' => $senderId ],
        'message' => [ 'text' => $answer ]
    ];
}
else
{
    $response = [
        'recipient' => [ 'id' => $senderId ],
        'message' => $answer
    ]; 
}
$ch = curl_init('https://graph.facebook.com/v2.6/me/messages?access_token='.$accessToken);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($response));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
if(!empty($input)){
$result = curl_exec($ch);
}
curl_close($ch);