<?php

/**
 * Class RemindBotCommand
 * This class schedules a reminder for the sender.
 *
 * Usage:
 *  REMIND <message> ON <date and time in YYYY-MM-DD hh:mm(:ss) format>
 *  REMIND <message> AFTER <number> <SECONDS | MINUTES | HOURS>
 *
 * @author: Angelito Sardez, Jr.
 * @date: 17/11/2017
 */
class RemindBotCommand extends BotCommand {
    public function __construct($sender, $user) {
        parent::__construct("REMIND", $sender, $user);
    }

    protected function executeCommand($parameter) {
        if (trim($parameter) == "") {
            $this->sendTextWithHelp("Sorry ".$this->user->getFirstName().", you need to specify the date and time, or a number and the unit of time when you want me to remind you, using either of these formats:".
                chr(10).
                "REMIND <message> ON <date and time in YYYY-MM-DD hh:mm format>".
                chr(10).
                "REMIND <message> AFTER <number> <SECONDS | MINUTES | HOURS>");
            return;
        }
        date_default_timezone_set('Asia/Manila');
        if (!$this->validateCommand($parameter)) {
            $sampleDateTime = new DateTime(date('Y-m-d H:i'));
            $sampleDateTime->add(new DateInterval('PT5M'));
            $this->sendTextWithHelp("Sorry ".$this->user->getFirstName().", I didn't get that. Please send your command in either of the following formats:".
                chr(10).
                "REMIND <message> ON <future date and time in YYYY-MM-DD hh:mm format> (e.g. REMIND me to send an email ON ".$sampleDateTime->format('Y-m-d H:i').")".
                chr(10).
                "REMIND <message> AFTER <number> <SECONDS | MINUTES | HOURS> (e.g. REMIND me to have a break AFTER 30 MINUTES)");
        }
        else {
            $isDatePartSpecific = $this->getIsTargetDate($parameter);
            $paramPart = explode($isDatePartSpecific ? " on " : " after ", strtolower($parameter));
            $dateTime = trim($paramPart[sizeof($paramPart) - 1]);
            if ($isDatePartSpecific) {
                if (!$this->validateDateTime($dateTime)) {
                    $this->send("Sorry ".$this->user->getFirstName().", I didn't get that date and time. Please give me a future date and time in YYYY-MM-DD hh:mm format.");
                    return;
                }
                if (!$this->validateDateTimeIfInFuture($dateTime)) {
                    $this->send("Hey ".$this->user->getFirstName().", the date and time you have given me is from the past. Please give me a future date and time for your reminder.");
                    return;
                }
            }
            else {
                $fexibleDatePart = explode(" ", $dateTime);
                $num = $fexibleDatePart[0];
                $unitOfTime = trim($fexibleDatePart[sizeof($fexibleDatePart) - 1]);
                if (!$this->validateNumber($num)) {
                    $this->send("Sorry ".$this->user->getFirstName().", the number of seconds, minutes, or hours I need should be a valid number and should be greater than 0.");
                    return;
                }
                if (!$this->validateUnitOfTime($unitOfTime)) {
                    $this->send("Sorry ".$this->user->getFirstName().", I do not recognize the unit of time you have given me. I only know second(s), minute(s), and hour(s), and their abbreviations.");
                    return;
                }
                $dateTime = $this->translateToDateTime($num, $unitOfTime);
            }
            $delay = strtotime($dateTime) - strtotime(date("Y-m-d H:i:s"));
            shell_exec('php /var/www/html/bot/ReminderScript.php '.$this->sender->getSenderId().' '.$this->sender->getAccessToken().' '.$this->user->getFirstName().' '.$delay.' '.urlencode('Remind '.$parameter).' > /dev/null 2>/dev/null &');
            $this->send("Got it, ".$this->user->getFirstName()."! I'll remind you when it's time.");
        }
    }

    function validateCommand($parameter) {
        return (strpos(strtolower($parameter), ' on ') !== false) || (strpos(strtolower($parameter), ' after ') !== false);
    }

    function getIsTargetDate($parameter) {
        return (strpos(strtolower($parameter), ' on ') !== false) ? true : false;
    }

    function validateDateTime($dateTime) {
        $formattedDateTime = DateTime::createFromFormat('Y-m-d H:i', $dateTime);
        $formattedDateTimeWithSec = DateTime::createFromFormat('Y-m-d H:i:s', $dateTime);
        return ($formattedDateTime && $formattedDateTime->format('Y-m-d H:i') === (new DateTime($dateTime))->format('Y-m-d H:i')) ||
                ($formattedDateTimeWithSec && $formattedDateTimeWithSec->format('Y-m-d H:i:s') === (new DateTime($dateTime))->format('Y-m-d H:i:s'));
    }

    function validateDateTimeIfInFuture($dateTime) {
        date_default_timezone_set('Asia/Manila');
        $givenDateTime = new DateTime($dateTime);
        $currentDateTime = new DateTime();
        return $givenDateTime > $currentDateTime;
    }

    function validateNumber($numberPart) {
        return is_numeric($numberPart) && $numberPart > 0;
    }

    function validateUnitOfTime($unitOfTime) {
        switch (strtolower(trim($unitOfTime))) {       
            case 's':           
            case 'sec':    
            case 'secs':
            case 'second':
            case 'seconds':
            case 'min':
            case 'mins':
            case 'minute':
            case 'minutes':
            case 'hr':
            case 'hrs':
            case 'hour':
            case 'hours':
                return true;
                break;
            default:
                return false;
        }
    }

    function translateToDateTime($num, $unitOfTime) {
        date_default_timezone_set('Asia/Manila');        
        $dateTime = new DateTime(date('Y-m-d H:i:s'));
        switch (strtolower(trim($unitOfTime))) {        
            case 's':     
            case 'sec':
            case 'secs':
            case 'second':
            case 'seconds':
                $dateTime->add(new DateInterval('PT'.$num.'S'));
                break;
            case 'min':
            case 'mins':
            case 'minute':
            case 'minutes':
                $dateTime->add(new DateInterval('PT'.$num.'M'));
                break;
            case 'hr':
            case 'hrs':
            case 'hour':
            case 'hours':
                $dateTime->add(new DateInterval('PT'.$num.'H'));
                break;
        }
        return $dateTime->format('Y-m-d H:i:s');
    }
}