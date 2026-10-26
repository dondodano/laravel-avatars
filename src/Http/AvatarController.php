<?php

namespace Dondodano\Avatars\Http;

use Dondodano\Avatars\Avatar;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AvatarController
{
    public function __invoke(Request $request): Response
    {
        $d = config('avatars.defaults');

        // Array-valued params (?size[]=1) fall back to defaults instead of erroring.
        $q = fn (string $key, mixed $default) => is_scalar($request->query($key)) ? $request->query($key) : $default;

        $avatar = Avatar::make((string) $q('name', 'John Doe'), [
            'size' => $q('size', $d['size']),
            'background' => $q('background', $d['background']),
            'color' => $q('color', $d['color']),
            'length' => $q('length', $d['length']),
            'font_size' => $q('font-size', $d['font_size']),
            'rounded' => $request->boolean('rounded', $d['rounded']),
            'bold' => $request->boolean('bold', $d['bold']),
            'uppercase' => $request->boolean('uppercase', $d['uppercase']),
            'format' => strtolower((string) $q('format', $d['format'])) === 'png' ? 'png' : 'svg',
        ]);

        return response($avatar->render(), 200, [
            'Content-Type' => $avatar->mime(),
            'Cache-Control' => 'public, max-age='.config('avatars.cache_ttl').', immutable',
        ]);
    }
}
