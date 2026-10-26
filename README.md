# dondodano/laravel-avatars

Self-hosted initials avatars for Laravel (SVG or PNG). A drop-in replacement for ui-avatars.com: same query parameters, no external dependency.

## Requirements

- PHP 8.2+ with `gd` (built with FreeType) and `mbstring`
- Laravel 11 or 12

## Installation

```bash
composer require dondodano/laravel-avatars
```

The service provider is auto-discovered. Optionally publish the config:

```bash
php artisan vendor:publish --tag=avatars-config
```

## Endpoint

```
GET /api/avatar?name=System+Administrator&background=random&size=128&rounded=true&bold=true&format=png
```

| Parameter | Default | Notes |
|---|---|---|
| `name` | `John Doe` | Initials are taken from the first letter of each word; a single word uses its first `length` letters |
| `size` | `64` | 16–512 px |
| `background` | `random` | Hex (`4CAF50`, `#4CAF50`, `abc`) or `random`; random is derived from the name, so it is stable per user |
| `color` | `ffffff` | Text color |
| `length` | `2` | Number of initials |
| `font-size` | `0.5` | Ratio of size, 0.1–1 |
| `rounded` | `false` | Circular crop |
| `bold` | `false` | |
| `uppercase` | `true` | |
| `format` | `svg` | `svg` or `png` |

Responses are cookie-free and sent with a one-year immutable `Cache-Control`. Path, middleware, cache lifetime and defaults are in `config/avatars.php`.

## PHP API

```php
use Dondodano\Avatars\Avatar;

Avatar::make('System Administrator')->size(128)->rounded()->bold()->format('png')->toUrl();
Avatar::make('Jane Doe')->toDataUri();   // embed, no HTTP request
Avatar::make('Jane Doe')->png();         // raw bytes, e.g. Storage::put()
```

`toUrl()` is absolute and built from `APP_URL`/the current request, so set `APP_URL` correctly if you store the URLs.

## Testing

```bash
composer install
composer test
composer format:check
```

## License

MIT. Bundled font: DejaVu Sans (free license, see https://dejavu-fonts.github.io/License.html).
