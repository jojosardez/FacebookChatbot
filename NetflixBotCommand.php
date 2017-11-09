<?php

class NetflixBotCommand extends BotCommand {
    public function __construct($sender, $user) {
        parent::__construct("NETFLIX", $sender, $user);
    }

    protected function executeCommand($parameter) {
        $this->send('getting response from instantwatcher');
        $htmlResult = file_get_contents('http://instantwatcher.com/search?content_type=1+2&source=1+2+3&q='.urlencode($parameter));
        $doc = new DOMDocument;
        $doc->preserveWhiteSpace = false;
        $doc->loadHTML($htmlResult);
        $xpath = new DOMXpath($doc);
        $this->send('response retrieved. performing xpath');
        $queryResult = $xpath->query('//div[@class="iw-title netflix-title list-title box-synopsis-mode"]');
        $this->send('xpath complete');
        foreach ($queryResult as $queryItem) {
            $title = $queryItem->xpath("./span[@class='title']/a");
            $this->send((string)$title);
            $year = $queryItem->xpath("./span[@class='year']/a");
            $this->send((string)$year);
        }
    }
}