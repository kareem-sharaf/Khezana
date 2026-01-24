<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Phase 5.3: Daily DB backup, 30-day retention (handled in db:backup)
Schedule::command('db:backup --compress')->daily()->at('02:00');

// Phase 6.1: Warm cache hourly for common pages
Schedule::command('cache:warm')->hourly();
