<?php

namespace App\Providers;

use App\Services\Ocr\OcrServiceInterface;
use App\Services\Ocr\PaddleOcrService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(OcrServiceInterface::class, PaddleOcrService::class);
    }

    public function boot(): void
    {
        //
    }
}
