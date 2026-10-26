<?php

namespace TestQ\Avatars;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use TestQ\Avatars\Http\AvatarController;

class AvatarServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/avatars.php', 'avatars');
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../config/avatars.php' => config_path('avatars.php'),
        ], 'avatars-config');

        Route::middleware(config('avatars.middleware', []))
            ->get(config('avatars.path'), AvatarController::class)
            ->name('avatars.show');
    }
}
