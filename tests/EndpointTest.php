<?php

namespace Dondodano\Avatars\Tests;

use Dondodano\Avatars\AvatarServiceProvider;

class EndpointTest extends TestCase
{
    public function test_it_serves_svg_by_default(): void
    {
        $response = $this->get('/api/avatar?name=Jane+Doe');

        $response->assertOk();
        $response->assertHeader('Content-Type', 'image/svg+xml');
        $this->assertStringContainsString('>JD</text>', $response->getContent());
    }

    public function test_it_serves_png(): void
    {
        $response = $this->get('/api/avatar?name=Jane+Doe&format=png&size=128&rounded=true&bold=true&background=random');

        $response->assertOk();
        $response->assertHeader('Content-Type', 'image/png');
        $this->assertStringStartsWith("\x89PNG", $response->getContent());
    }

    public function test_responses_are_cacheable_and_cookie_free(): void
    {
        $response = $this->get('/api/avatar?name=Jane+Doe');

        $this->assertStringContainsString('max-age=31536000', $response->headers->get('Cache-Control'));
        $this->assertEmpty($response->headers->getCookies());
    }

    public function test_malformed_input_does_not_error(): void
    {
        $this->get('/api/avatar?name[]=x&background[]=1&color[]=1&length[]=1&format[]=png&size[]=1&font-size[]=1')->assertOk();
        $this->get('/api/avatar?size=abc&font-size=99&format=gif&background=zzz')->assertOk();
    }

    public function test_path_is_configurable(): void
    {
        $this->app['config']->set('avatars.path', 'img/avatar');
        (new AvatarServiceProvider($this->app))->boot();

        $this->get('/img/avatar?name=Jane+Doe')->assertOk();
    }
}
