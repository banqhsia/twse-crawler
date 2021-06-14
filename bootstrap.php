<?php

use App\Client;
use App\Clock;
use App\Message\Envelope;
use App\Provider\Market\TwseHolidayScheduleProvider;
use App\Type\Value;
use Illuminate\Cache\CacheManager;
use Illuminate\Container\Container;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Facade;
use Longman\TelegramBot\Request as TelegramRequest;
use Longman\TelegramBot\Telegram;

require __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::create(__DIR__);
$dotenv->load();

$container = Container::getInstance();

$container->singleton(Clock::class, function () {
    // CarbonImmutable::setTestNow('2021-06-10 16:00 +08:00');
    return (new Clock)->touchNow();
});

/**
 * Telegram Client bindings
 */
$container->when(Telegram::class)
    ->needs('$api_key')
    ->give(env('TELEGRAM_BOT_TOKEN'));

$container->when(Envelope::class)
    ->needs('$receiver')
    ->give(env('TELEGRAM_CHAT_ID'));

$container->singleton(TelegramRequest::class, function () use ($container) {
    $request = new TelegramRequest;
    $request->initialize($container->get(Telegram::class));

    return $request;;
});

/**
 * Application config bindings.
 */
$container->singleton('config', function () {
    return [
        'cache.default' => 'files',
        'cache.stores.files' => [
            'driver' => 'file',
            'path' => __DIR__ . '/cache',
        ],
    ];
});

$container->singleton('files', Filesystem::class);
$container->instance('cache', new CacheManager($container));

Facade::setFacadeApplication($container);

/**
 * Ensure Clock::$tradingHolidays is exists.
 */
$clock = $container->get(Clock::class);
$holidayKey = "twse:holiday:{$clock->getNow()->year}";

$holidays = Cache::remember($holidayKey, $clock->getNow()->addWeeks(), function () use ($container) {
    $schedules = $container->call(Client::class . "@get", [new TwseHolidayScheduleProvider]);

    return collect($schedules)->reject(function ($schedule) {
        return $schedule->isTradingDay();
    })->transform(function ($schedule) {
        return $schedule->getDate();
    })->values()->all();
});

$clock->setTradingHolidays($holidays);

/**
 * Custom helpers.
 */
/**
 * Create a Value instance.
 *
 * @param int|float $value
 * @return Value
 */
function v($value)
{
    return Value::make($value);
}
