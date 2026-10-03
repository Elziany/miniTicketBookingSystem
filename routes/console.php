<?php

use App\Jobs\ExpirePendingApprovalsJob;
use App\Jobs\SendEventRemindersJob;
use App\Jobs\SendPostEventFeedbackJob;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');
Schedule::job(new SendEventRemindersJob)->everyMinute();
Schedule::job(new ExpirePendingApprovalsJob)->everyMinute();
Schedule::job(new \App\Jobs\ExpireHeldReservationsJob)->everyMinute();
Schedule::job(new SendPostEventFeedbackJob)->everyMinute();
