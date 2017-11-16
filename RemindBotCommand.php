<?php

/**
 * Class RemindBotCommand
 * This class schedules a reminder for the sender.
 *
 * Usage:
 *  REMIND <message> ON <date and time in YYYY-MM-DD hh:mm(:ss) format>
 *
 * @author: Angelito Sardez, Jr.
 * @date: 16/11/2017
 */
class RemindBotCommand extends BotCommand {
    public function __construct($sender, $user) {
        parent::__construct("REMIND", $sender, $user);
    }

    protected function executeCommand($parameter) {
        if (trim($parameter) == "") {
            $this->sendTextWithHelp("Sorry ".$this->user->getFirstName().", you need to specify the date and time when you want me to remind you. (e.g. REMIND <message> ON <date and time in YYYY-MM-DD hh:mm format>)");
            return;
        }
        date_default_timezone_set('Asia/Manila');
        if (!$this->validateCommand($parameter)) {
            $sampleDateTime = new DateTime(date('Y-m-d H:i'));
            $sampleDateTime->add(new DateInterval('PT5M'));
            $this->sendTextWithHelp("Sorry ".$this->user->getFirstName().", I didn't get that. Please send your command in the correct format: REMIND <message> ON <future date and time in YYYY-MM-DD hh:mm format> (e.g. REMIND me to send an email ON ".$sampleDateTime->format('Y-m-d H:i').")");
        }
        else {
            $paramPart = explode(" on ", $parameter);
            $dateTime = trim($paramPart[sizeof($paramPart) - 1]);
            if (!$this->validateDateTime($dateTime)) {
                $this->sendTextWithHelp("Sorry ".$this->user->getFirstName().", I didn't get that date and time. Please give me a future date and time in YYYY-MM-DD hh:mm format.");
                return;
            }
            if (!$this->validateDateTimeIfInFuture($dateTime)) {
                $this->send("Hey ".$this->user->getFirstName().", the date and time you have given me is from the past. Please give me a future date and time for your reminder.");
                return;
            }
            $delay = strtotime($dateTime) - strtotime(date("Y-m-d H:i:s"));
            shell_exec('php /var/www/html/bot/ReminderScript.php '.$this->sender->getSenderId().' '.$this->sender->getAccessToken().' '.$this->user->getFirstName().' '.$delay.' '.urlencode('Remind '.$parameter).' > /dev/null 2>/dev/null &');
            $this->send("Got it, ".$this->user->getFirstName()."! I'll remind you when it's time.");
        }
    }

    function validateCommand($parameter) {
        return strpos(strtolower($parameter), ' on ') !== false;
    }

    function validateDateTime($dateTime) {
        $formattedDateTime = DateTime::createFromFormat('Y-m-d H:i:s', $dateTime);
        return $formattedDateTime && $formattedDateTime->format('Y-m-d H:i:s') === $dateTime;
    }

    function validateDateTimeIfInFuture($dateTime) {
        date_default_timezone_set('Asia/Manila');
        $givenDateTime = new DateTime($dateTime);
        $currentDateTime = new DateTime();
        return $givenDateTime > $currentDateTime;
    }
}