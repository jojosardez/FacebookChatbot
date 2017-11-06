<?php

class ImdbBotCommand extends BotCommand {
    public function __construct() {
        parent::__construct("IMDB");
    }

    protected function executeCommand($parameter) {
        return ["attachment"=>[
            "type"=>"template",
            "payload"=>[
              "template_type"=>"generic",
              "elements"=>[
                [
                  "title"=>$this->getCommand()." command not yet implemented. Parameter passed: ".$parameter,
                  "image_url"=>"https://cdn.elegantthemes.com/blog/wp-content/uploads/2016/03/500-internal-server-error.jpg",
                  "subtitle"=>$this->getCommand()." command not yet implemented. Parameter passed: ".$parameter
                ]
              ]
            ]
          ]];
    }
}