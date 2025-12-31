<?php

namespace App\Providers;

use App\Services\Ocr\OcrServiceInterface;
use App\Services\Ocr\PaddleOcrService;
use Illuminate\Support\ServiceProvider;

/**
 * Application Service Provider
 * 
 * Registers service bindings for dependency injection.
 */
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register services
     * 
     * @return void
     */
    public function register(): void
    {
        // Bind OCR service interface to PaddleOCR implementation
        $this->app->bind(OcrServiceInterface::class, PaddleOcrService::class);
    }

    /**
     * Bootstrap services
     * 
     * @return void
     */
    public function boot(): void
    {
        //
    }
}
