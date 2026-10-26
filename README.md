# testq/avatars

Self-hosted replacement for ui-avatars.com. Same query parameters, no external dependency.

    GET /api/avatar?name=System+Administrator&background=random&size=128&rounded=true&bold=true&format=png

Parameters: `name`, `size` (16-512), `background` (hex or `random`), `color`, `length`, `font-size` (0.1-1), `rounded`, `bold`, `uppercase`, `format` (`svg`|`png`).
`background=random` is derived from the name, so it is stable per user.

    use TestQ\Avatars\Avatar;

    Avatar::make('System Administrator')->size(128)->rounded()->bold()->format('png')->toUrl();
    Avatar::make('Jane Doe')->toDataUri();   // embed, no HTTP request
    Avatar::make('Jane Doe')->png();         // raw bytes, e.g. Storage::put()

Publish config: `php artisan vendor:publish --tag=avatars-config`

Bundled font: DejaVu Sans (free license).
