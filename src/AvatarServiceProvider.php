<?php

namespace Dondodano\Avatars;

use Dondodano\Avatars\Http\AvatarController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

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
