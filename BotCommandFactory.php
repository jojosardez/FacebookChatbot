<?php
/**
 * Class BotCommandFactory
 * This class creates an instance of BotCommand based on the given command.
 *
 * @author: Angelito Sardez, Jr.
 * @date: 14/11/2017
 */

class BotCommandFactory {
    public static function create($command, $sender, $user) {
        $commandLower = strtolower($command);
        switch($commandLower)
        {
            case "help":
                return new HelpBotCommand($sender, $user);
                break;
            case "echo":
                return new EchoBotCommand($sender, $user);
                break;
            case "hi":
                return new HiBotCommand($sender, $user);
                break;
            case "imdb":
                return new ImdbBotCommand($sender, $user);
                break;
            case "cinema":
                return new CinemaBotCommand($sender, $user);
                break;
            case "php":
                return new PhpBotCommand($sender, $user);
                break;
            case "weather":
                return new WeatherBotCommand($sender, $user);
                break;
            case "phone":
                return new PhoneBotCommand($sender, $user);
                break;
            case "gender":
                return new GenderBotCommand($sender, $user);
                break;
            case "recipe":
                return new RecipeBotCommand($sender, $user);
                break;
            case "pokedex":
                return new PokedexBotCommand($sender, $user);
                break;
            case "ip":
                return new IpBotCommand($sender, $user);
                break;
            case "history":
                return new HistoryBotCommand($sender, $user);
                break;
            case "trump":
                return new TrumpBotCommand($sender, $user);
                break;
            case "university":
                return new UniversityBotCommand($sender, $user);
                break;
            case "netflix":
                return new NetflixBotCommand($sender, $user);
                break;
            case "remind":
                return new RemindBotCommand($sender, $user);
                break;
            case "ask":
                return new MagicBotCommand($sender, $user);
                break;
            default:
                return new UnknownBotCommand($command, $sender, $user);
        }
    }
}