<?php

namespace TestQ\Avatars\Http;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use TestQ\Avatars\Avatar;

class AvatarController
{
    public function __invoke(Request $request): Response
    {
        $d = config('avatars.defaults');

        $avatar = Avatar::make((string) $request->query('name', 'John Doe'), [
            'size' => $request->query('size', $d['size']),
            'background' => $request->query('background', $d['background']),
            'color' => $request->query('color', $d['color']),
            'length' => $request->query('length', $d['length']),
            'font_size' => $request->query('font-size', $d['font_size']),
            'rounded' => $request->boolean('rounded', $d['rounded']),
            'bold' => $request->boolean('bold', $d['bold']),
            'uppercase' => $request->boolean('uppercase', $d['uppercase']),
            'format' => strtolower((string) $request->query('format', $d['format'])) === 'png' ? 'png' : 'svg',
        ]);

        return response($avatar->render(), 200, [
            'Content-Type' => $avatar->mime(),
            'Cache-Control' => 'public, max-age='.config('avatars.cache_ttl').', immutable',
        ]);
    }
}
