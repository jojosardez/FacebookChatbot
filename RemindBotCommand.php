<?php

class RemindBotCommand extends BotCommand {
    public function __construct($sender, $user) {
        parent::__construct("REMIND", $sender, $user);
    }

    protected function executeCommand($parameter) {
        shell_exec('php /var/www/html/bot/ReminderScript.php '.$this->sender->getSenderId().' '.$this->sender->getAccessToken().' '.$this->user->getFirstName().' '.'10'.' '.urlencode('Remind '.$parameter).' > /dev/null 2>/dev/null &');
        $this->send("Got it, ".$this->user->getFirstName()."!. I'll remind you when it's time.");
    }
}