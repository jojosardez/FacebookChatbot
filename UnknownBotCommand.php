<?php

class UnknownBotCommand extends BotCommand {
    public function __construct($command, $sender, $user) {
        parent::__construct($command, $sender, $user);
    }

    protected function executeCommand($parameter) {
        $this->send(["attachment"=>[
            "type"=>"template",
            "payload"=>[
                "template_type"=>"button",
                "text"=>"Sorry, ".$this->user->getFirstName().". I don't know the command \"".$this->command."\". You may want to check the commands I support by typing \"HELP\" or by clicking the button below.",
            "buttons"=>[
                [
                    "type"=>'postback',
                    "title"=>'Display Help',
                    "payload"=>'help'
                ]
            ]
            ]
        ]]);
    }
}