<?php

use App\Services\ReactionBatchService;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::call(fn () => app(ReactionBatchService::class)->flushAll())
    ->everyMinute()
    ->name('flush-pending-reactions')
    ->withoutOverlapping();
