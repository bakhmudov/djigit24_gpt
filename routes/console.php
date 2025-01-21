<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Schedule::command('bitrix:sync-departments')->dailyAt('23:45');
Schedule::command('bitrix:sync-users')->dailyAt('23:50');
Schedule::command('bitrix:sync-tasks')->dailyAt('23:55');
