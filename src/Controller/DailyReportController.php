<?php

namespace App\Controller;

use App\Client;
use App\Clock;
use App\Message\Envelope;
use App\Message\Message;
use App\Message\Pigeon;
use App\Provider\Filter\Filterable;
use App\Provider\Options\TaifexFuturesProvider;
use App\Provider\Options\TaifexInstitutionProvider;
use App\Provider\Options\TaifexPutCallRatioProvider;
use App\Provider\Securities\TwseInformationProvider;
use App\Provider\TwseProvider;
use App\Symbol;
use App\Transformer\DailyReportTransformer;
use Illuminate\Container\Container;

class DailyReportController
{
    /**
     * @var Client
     */
    private $client;

    /**
     * @var Clock
     */
    private $clock;

    /**
     * Construct.
     *
     * @param Client $client
     * @param Clock $clock
     */
    public function __construct(Client $client, Clock $clock)
    {
        $this->client = $client;
        $this->clock = $clock;
    }

    /**
     * Send the daily report to the target channel or chat.
     *
     * @param DailyReportTransformer $transformer
     * @param Pigeon $pigeon
     * @return void
     */
    public function sendDailyReport(DailyReportTransformer $transformer, Pigeon $pigeon)
    {
        $container = Container::getInstance();

        $previousDay = $this->clock->getLastTradingDay();
        $currentDay = $this->clock->getCurrentTradingDay();

        // TWSE index
        $codes = Symbol::createSymbolsFromEnvString('tse.t00');
        $twseIndex = (new TwseProvider)->setSymbols($codes);

        $transformer->setTwseIndex(
            $this->request($twseIndex)->first()
        );

        // TWSE market information
        $twseInfo = $container->get(TwseInformationProvider::class);

        $transformer->setTwseInfo(
            $this->request($twseInfo)->first()
        );

        // TAIFEX Futures
        $futures = $container->get(TaifexFuturesProvider::class)
            ->setSymbol('TX')
            ->setDate($currentDay);

        $transformer->setFuturesInfo(
            $this->request($futures)
        );

        // TAIFEX Institution
        $commodity = Container::getInstance()->get(TaifexInstitutionProvider::class)
            ->setDatePeriod(
                $previousDay->toPeriod($currentDay)
            );

        $transformer->setInstitutionCommodity(
            $this->request($commodity)
        );

        // TAIFEX put call ratio (P/C ratio)
        $pcRatio = new TaifexPutCallRatioProvider;

        $transformer->setPutCallRatioInfo(
            $this->request($pcRatio)
        );

        $envelope = Envelope::encapsulate(
            new Message($transformer->transform())
        );

        return $pigeon->send($envelope);
    }

    /**
     * Send the request.
     *
     * @param mixed $provider
     * @return mixed
     */
    protected function request($provider)
    {
        $result = collect($this->client->get($provider));

        if ($provider instanceof Filterable) {
            $result = call_user_func($provider->getFilter(), $result);
        }

        return $result;
    }
}
