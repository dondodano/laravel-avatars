<?php

namespace Dondodano\Avatars\Tests;

use Dondodano\Avatars\Avatar;

class AvatarTest extends TestCase
{
    public function test_it_builds_initials_from_words(): void
    {
        $svg = Avatar::make('System Administrator')->svg();

        $this->assertStringContainsString('>SA</text>', $svg);
    }

    public function test_single_word_uses_leading_characters(): void
    {
        $this->assertStringContainsString('>JA</text>', Avatar::make('Jane')->svg());
        $this->assertStringContainsString('>J</text>', Avatar::make('Jane')->length(1)->svg());
    }

    public function test_uppercase_can_be_disabled(): void
    {
        $this->assertStringContainsString('>sa</text>', Avatar::make('system administrator')->uppercase(false)->svg());
    }

    public function test_initials_are_xml_escaped(): void
    {
        $svg = Avatar::make('<b> &x')->svg();

        $this->assertStringNotContainsString('<b>', $svg);
        $this->assertNotFalse(simplexml_load_string($svg));
    }

    public function test_random_background_is_stable_per_name(): void
    {
        $this->assertSame(Avatar::make('Jane Doe')->svg(), Avatar::make('jane doe')->svg());
        $this->assertNotSame(Avatar::make('Jane Doe')->svg(), Avatar::make('John Smith')->svg());
    }

    public function test_colors_accept_short_hex_and_fall_back_when_invalid(): void
    {
        $this->assertStringContainsString('fill="#4caf50"', Avatar::make('A B')->background('#4CAF50')->svg());
        $this->assertStringContainsString('fill="#aabbcc"', Avatar::make('A B')->background('abc')->svg());
        $this->assertStringContainsString('fill="#ffffff"', Avatar::make('A B')->color('not-a-color')->svg());
    }

    public function test_size_is_clamped(): void
    {
        $this->assertStringContainsString('width="512"', Avatar::make('A B')->size(9999)->svg());
        $this->assertStringContainsString('width="16"', Avatar::make('A B')->size(1)->svg());
    }

    public function test_rounded_sets_radius(): void
    {
        $this->assertStringContainsString('rx="32"', Avatar::make('A B')->size(64)->rounded()->svg());
    }

    public function test_png_has_requested_dimensions(): void
    {
        $png = Avatar::make('System Administrator')->size(128)->rounded()->bold()->png();

        $this->assertStringStartsWith("\x89PNG", $png);
        $this->assertSame([128, 128], array_slice(getimagesizefromstring($png), 0, 2));
    }

    public function test_rounded_png_has_transparent_corners(): void
    {
        $img = imagecreatefromstring(Avatar::make('A B')->size(64)->rounded()->png());

        $this->assertSame(127, (imagecolorat($img, 0, 0) >> 24) & 0x7F);
        $this->assertSame(0, (imagecolorat($img, 2, 32) >> 24) & 0x7F);
    }

    public function test_data_uri_and_url(): void
    {
        $this->assertStringStartsWith('data:image/png;base64,', Avatar::make('A B')->format('png')->toDataUri());
        $this->assertStringContainsString('/api/avatar?name=A+B', Avatar::make('A B')->toUrl());
    }
}
