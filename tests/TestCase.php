<?php

namespace Dondodano\Avatars\Tests;

use Dondodano\Avatars\AvatarServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [AvatarServiceProvider::class];
    }
}
