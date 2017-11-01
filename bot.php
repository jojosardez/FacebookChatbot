<?php
// parameters
$hubVerifyToken = 'Is238Group5Chatbot';
$accessToken =   "EAAKiQ7FkXIgBAFOk0PTq6zD4zbllh9IBh5wtUb0saYRZCE3LfRgynuCZAZBb2eiQHKi4tsKHTxsjDEqjkxSUFKDfRZCysrvk0ZCtIJgyrjhte8Fvt1XgXZCHxPZA6BZCpZBxgnY9EFM3io0l9cgZCvgVspEdQJ6SwqF275UdZCHyOa4ygfESonKrfdR";
// check token at setup
if ($_REQUEST['hub_verify_token'] === $hubVerifyToken) {
  echo $_REQUEST['hub_challenge'];
  exit;
}
// handle bot's anwser
$input = json_decode(file_get_contents('php://input'), true);
$senderId = $input['entry'][0]['messaging'][0]['sender']['id'];
$messageText = $input['entry'][0]['messaging'][0]['message']['text'];
$command = strtolower(trim(explode(" ", $messageText)[0]));
$response = null;
//set Message
switch($command)
{
    case "echo":
        $answer = substr($messageText, 5);
        if ($answer == "")
        {
            $answer = "Nothing to echo, human!";
        }
        break;
    case "hi":
        $answer = "Hello";
        break;
    case "imdb":
        $parameter = substr($messageText, 5);
        if ($parameter == "")
        {
            $answer = "Movie title not provided, human!";
        }
        else 
        {
            $result = json_decode(file_get_contents('http://www.omdbapi.com/?t='.$parameter.'&apikey=BanMePlz'), true);
            $answer = ["attachment"=>[
                "type"=>"template",
                "payload"=>[
                  "template_type"=>"generic",
                  "elements"=>[
                    [
                      "title"=>$result['Title'].' ('.$result['Year'].').',
                      "item_url"=>'http://www.imdb.com/title/'.$result['imdbID'].'/',
                      "image_url"=>$result['Poster'],
                      "subtitle"=>$result['Plot'].' Starring: '.$result['Actors'].'. Rating: '.$result['imdbRating'].'."'
                    ]
                  ]
                ]
              ]];
        }
        break;
    default:
        $answer = "Group 5 Chatbot scratched its head. It is too primitive to understand ''" . $messageText . "''. Group 5 Chatbot's screws are loosening... :(";
}
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