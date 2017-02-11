<?php


namespace App\Bot\Middleware;

use Mpociot\BotMan\Interfaces\DriverInterface;
use Mpociot\BotMan\Interfaces\MiddlewareInterface;
use Mpociot\BotMan\Message;


class LoggerMiddleware implements MiddlewareInterface
{
    public function handle(Message &$message, DriverInterface $driver)
    {
        \Log::debug("[handle] Msg " . $message->getMessage());
    }

    public function isMessageMatching(Message $message, $test, $regexMatched)
    {
        \Log::debug("isMatching ($test) " . $message->getMessage(), ['regex' => $regexMatched]);

        return true;
    }
}