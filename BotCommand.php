<?php

/**
 * Class BotCommand
 * This is the base class of all bot command implementations.
 *
 * @author: Angelito Sardez, Jr.
 * @date: 12/11/2017
 */
abstract class BotCommand {
    public function __construct($command, $sender, $user) {
        $this->command = $command;
        $this->sender = $sender;
        $this->user = $user;
    }

    protected $command;   
    protected $sender;   
    protected $user;   
    abstract protected function executeCommand($parameter);

    public function execute($parameter) {
        $this->executeCommand($parameter);
    }

    protected function send($message) {
        $this->sender->send($message);
    }

    protected function sendTextWithHelp($message, $showCompleteHelp = true) {
        $template = ["attachment"=>[
            "type"=>"template",
            "payload"=>[
                "template_type"=>"button",
                "text"=>$message,
            "buttons"=>[
                [
                    "type"=>'postback',
                    "title"=>'See '.$this->command.' Help',
                    "payload"=>'help '.$this->command
                ]
            ]
            ]
        ]];
        if ($showCompleteHelp) {
            $template['attachment']['payload']['buttons'][] = [
                "type"=>'postback',
                "title"=>'See Complete Help',
                "payload"=>'help'
            ];
        }
        $this->sender->send($template);
    }

    protected function sendAction($action) {
        $this->sender->sendAction($action);
    }
}