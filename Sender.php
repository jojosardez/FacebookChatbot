<?php

class Sender {
    public function __construct($senderId, $accessToken) {
        $this->senderId = $senderId;
        $this->accessToken = $accessToken;
    }

    protected $senderId;
    protected $accessToken;

    public function getSenderId() {
        return $this->senderId;
    }

    public function getAccessToken() {
        return $this->accessToken;
    }

    public function send($message) {
        if (is_string($message)) {
            $messageToSend = [
                'recipient' => [ 'id' => $this->senderId ],
                'message' => [ 'text' => $message ]
            ];
        }
        else {
            if(!empty($message)) {
                $messageToSend = [
                    'recipient' => [ 'id' => $this->senderId ],
                    'message' => $message
                ];
            }
        }
        if(!empty($messageToSend)){
            $ch = curl_init('https://graph.facebook.com/v2.11/me/messages?access_token='.$this->accessToken);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($messageToSend));
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
            $result = curl_exec($ch);
            curl_close($ch);
        }
    }

    public function sendAction($action) {
        $actionText = '';
        if ($action == SenderAction::markSeen) {
            $actionText = 'mark_seen';
        }
        else if ($action == SenderAction::typingOn) {
            $actionText = 'typing_on';
        }
        else if ($action == SenderAction::typingOff) {
            $actionText = 'typing_off';
        }

        if ($actionText != '') {
            $actionToSend = [
                'recipient' => [ 'id' => $this->senderId ],
                'sender_action' => $actionText
            ];
            $ch = curl_init('https://graph.facebook.com/v2.11/me/messages?access_token='.$this->accessToken);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($actionToSend));
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
            $result = curl_exec($ch);
            curl_close($ch);
        }
    }
}