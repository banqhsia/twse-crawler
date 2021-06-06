<?php

use Illuminate\Container\Container;
use Longman\TelegramBot\Request as TelegramRequest;
use Longman\TelegramBot\Telegram;

require __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::create(__DIR__);
$dotenv->load();

$container = Container::getInstance();

$container->when(Telegram::class)
    ->needs('$api_key')
    ->give(env('TELEGRAM_BOT_TOKEN'));

$container->singleton(TelegramRequest::class, function () use ($container) {
    $request = new TelegramRequest;
    $request->initialize($container->get(Telegram::class));

    return $request;;
});
