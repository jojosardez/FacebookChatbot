<?php

class ImdbBotCommand extends BotCommand {
    public function __construct($sender, $user) {
        parent::__construct("IMDB", $sender, $user);
    }

    protected function executeCommand($parameter) {
        $this->send(["attachment"=>[
            "type"=>"template",
            "payload"=>[
              "template_type"=>"generic",
              "elements"=>[
                [
                  "title"=>$this->getCommand()." command is not yet implemented. Parameter passed: ".$parameter,
                  "image_url"=>"https://cdn.elegantthemes.com/blog/wp-content/uploads/2016/03/500-internal-server-error.jpg",
                  "subtitle"=>$this->getCommand()." command is not yet implemented. Parameter passed: ".$parameter
                ]
              ]
            ]
          ]]);
    }
}