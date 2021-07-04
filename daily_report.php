<?php

require __DIR__ . "/bootstrap.php";

use App\Controller\DailyReportController;

$container->call(DailyReportController::class . '@sendDailyReport');
