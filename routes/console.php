<?php

use Illuminate\Support\Facades\Artisan;

Artisan::command('enspire:about', function () {
    $this->info('Enspire Learning - Laravel LMS');
})->purpose('Display Enspire Learning application information.');
