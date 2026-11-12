<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Models\IdempotencyKey;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Tambahkan penjadwalan model:prune di sini
Schedule::command('model:prune', [
    '--model' => [IdempotencyKey::class],
])->daily();