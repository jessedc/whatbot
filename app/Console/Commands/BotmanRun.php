<?php

namespace App\Console\Commands;

use App\Bot\Commands\Stocks;
use App\Bot\Middleware\LoggerMiddleware;
use Illuminate\Console\Command;
use Mpociot\BotMan\BotManFactory;
use React\EventLoop\Factory;

class BotmanRun extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'botman:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run Botman';

    /**
     * @var \Mpociot\BotMan\BotMan
     */
    protected $botman;

    /**
     * @var
     */
    protected $loop;

    public function __construct()
    {
        parent::__construct();

        $this->loop = Factory::create();

        $this->botman = BotManFactory::createForRTM([
            'slack_token' => config('services.botman.slack_token')
        ], $this->loop);

    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->botman->hears('([A-Z\.]{2,10})', Stocks::class.'@handleQuote');

//        $this->botman->group(['middleware' => new LoggerMiddleware()], function ($bot) {
//            $bot->hears('(.*)', function($bot, $match) {
////                echo $match;
//            });
//        });

//        $this->botman->hears('(.*)', function($bot, $match) {
//            echo $match;
//        })->middleware(new LoggerMiddleware());

//        $this->botman->hears('.*', Stocks::class.'@handleNull')
//            ->middleware(new LoggerMiddleware());

        $this->info("Running EventLoop");

        $this->loop->run();
    }
}
