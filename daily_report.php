<?php

require __DIR__ . "/bootstrap.php";

use App\Controller\DailyReportController;

$response = $container->call(DailyReportController::class . '@sendDailyReport');

echo $response->toJson();
