<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
|--------------------------------------------------------------------------
| WA Bridge scheduler
|--------------------------------------------------------------------------
|
| Hostinger cron invokes `php artisan schedule:run` once a minute (see
| DEPLOY.md, Cron 1). This file is scheduler responsibility ONLY —
| materializing/dispatching due automation work. It must NOT also drive
| the queue worker: that's a separate cron entry (Cron 2, calling
| `queue:work` directly), kept independent so the scheduler process never
| spawns or waits on a worker subprocess, and so a slow/stuck queue batch
| can't cause the scheduler tick itself to be late or skipped.
|
| withoutOverlapping() matters even at this foundation stage: it's the
| first half of the idempotency guarantee the automation engine depends
| on later (the other half — the atomic claim query — is added with the
| real automation_execution_steps table).
*/
Schedule::command('automation:generate-executions')
    ->everyMinute()
    ->withoutOverlapping();
