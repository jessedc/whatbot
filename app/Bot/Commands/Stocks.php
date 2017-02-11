<?php


namespace App\Bot\Commands;

use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\Response;
use League\Csv\Reader;
use Mpociot\BotMan\BotMan;

class Stocks
{
    public function handleQuote(BotMan $bot, $quote) {

        if (empty($quote)) {
            return;
        }

        \Log::debug("Fetching quote $quote");

        $now = Carbon::now(new \DateTimeZone('UTC'))->startOfDay();

        $yesterday = $now->copy();
        $dayBefore = $yesterday->copy()->subDay(1);

        $params = [
            's' => $quote,
            'a' => $dayBefore->format('n') - 1, //from Month,
            'b' => $dayBefore->format('j'),  //from day
            'c' => $dayBefore->format('Y'),  //from year
            'd' => $yesterday->format('n') - 1,
            'e' => $yesterday->format('j'),
            'f' => $yesterday->format('Y')
        ];

        $client = new Client();
        try {
            $response = $client->request(
                'GET',
                'http://chart.finance.yahoo.com/table.csv',
                ['query' => $params]
            );

            $reader = Reader::createFromString($response->getBody());

            $price = 0;
            $date = '1-1-1970';
            foreach ($reader as $index => $row) {
                if ($index == 0) continue;

                $price = $row[4];
                $date = $row[0];

                break;
            }

            $price = sprintf('$%0.3f', $price);

            $bot->reply("$quote closed at $price on $date.");
        } catch (RequestException $e) {

            //TODO: Cache results that 404 to prevent web calls.

            $code = $e->getResponse()->getStatusCode();
            $uri = (string) $e->getRequest()->getUri();
            if ($code == Response::HTTP_NOT_FOUND) {
                \Log::warning("Quote $quote not found (404). see: $uri.");
            } else {
                \Log::error("Quote $quote failed. HTTP $code see: $uri.");
            }
        }
    }

}