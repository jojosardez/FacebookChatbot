<?php

class BotCommandFactory {
    public static function create($command) {
        $commandLower = strtolower($command);
        switch($commandLower)
        {
            case "echo":
                return new EchoBotCommand();
                break;
            case "hi":
                return new HiBotCommand();
                break;
            case "imdb":
                return new ImdbBotCommand();
                break;
            case "cinema":
                return new CinemaBotCommand();
                break;
            case "php":
                return new PhpBotCommand();
                break;
            case "weather":
                return new WeatherBotCommand();
                break;
            case "phone":
                return new PhoneBotCommand();
                break;
            case "gender":
                return new GenderBotCommand();
                break;
            case "recipe":
                return new RecipeBotCommand();
                break;
            case "pokedex":
                return new PokedexBotCommand();
                break;
            case "ip":
                return new IpBotCommand();
                break;
            case "history":
                return new HistoryBotCommand();
                break;
            case "trump":
                return new TrumpBotCommand();
                break;
            case "university":
                return new UniversityBotCommand();
                break;
            case "netflix":
                return new NetflixBotCommand();
                break;
            case "remind":
                return new RemindBotCommand();
                break;
            default:
                return new UnknownBotCommand($command);
        }
    }
}