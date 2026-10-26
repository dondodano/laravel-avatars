<?php

namespace Dondodano\Avatars;

use GdImage;
use RuntimeException;

class Renderer
{
    private const SUPERSAMPLE = 4;

    private array $o;

    public function __construct(private string $name, array $options)
    {
        $d = config('avatars.defaults');

        $this->o = [
            'size' => max(config('avatars.min_size'), min(config('avatars.max_size'), (int) $options['size'])),
            'length' => max(1, min(10, (int) $options['length'])),
            'font_size' => max(0.1, min(1, (float) $options['font_size'])),
            'rounded' => (bool) $options['rounded'],
            'bold' => (bool) $options['bold'],
            'uppercase' => (bool) $options['uppercase'],
            'color' => $this->hex((string) $options['color'], $d['color']),
            'background' => $this->resolveBackground((string) $options['background'], $d['background']),
        ];
    }

    public function initials(): string
    {
        $words = preg_split('/[\s_\-.,]+/u', trim($this->name), -1, PREG_SPLIT_NO_EMPTY) ?: [];
        $length = $this->o['length'];

        if (count($words) <= 1) {
            $initials = mb_substr($words[0] ?? '?', 0, $length);
        } else {
            $initials = implode('', array_map(fn ($w) => mb_substr($w, 0, 1), array_slice($words, 0, $length)));
        }

        return $this->o['uppercase'] ? mb_strtoupper($initials) : $initials;
    }

    public function svg(): string
    {
        $s = $this->o['size'];
        $text = htmlspecialchars($this->initials(), ENT_XML1 | ENT_QUOTES, 'UTF-8');
        $weight = $this->o['bold'] ? 'bold' : 'normal';
        $fontSize = round($s * $this->o['font_size'], 2);
        $radius = $this->o['rounded'] ? $s / 2 : 0;
        $bg = $this->o['background'];
        $fg = $this->o['color'];

        return '<svg xmlns="http://www.w3.org/2000/svg" width="'.$s.'" height="'.$s.'" viewBox="0 0 '.$s.' '.$s.'">'
            .'<rect width="'.$s.'" height="'.$s.'" rx="'.$radius.'" fill="#'.$bg.'"/>'
            .'<text x="50%" y="50%" dy=".35em" text-anchor="middle" fill="#'.$fg.'" '
            ."font-family=\"'DejaVu Sans',Verdana,Arial,sans-serif\" font-size=\"".$fontSize.'" font-weight="'.$weight.'">'
            .$text.'</text></svg>';
    }

    public function png(): string
    {
        $size = $this->o['size'] * self::SUPERSAMPLE;
        $img = imagecreatetruecolor($size, $size);
        imagealphablending($img, false);
        imagesavealpha($img, true);
        imagefill($img, 0, 0, imagecolorallocatealpha($img, 0, 0, 0, 127));

        $bg = $this->allocate($img, $this->o['background']);
        $fg = $this->allocate($img, $this->o['color']);

        if ($this->o['rounded']) {
            imagefilledellipse($img, intdiv($size, 2), intdiv($size, 2), $size - 1, $size - 1, $bg);
        } else {
            imagefilledrectangle($img, 0, 0, $size - 1, $size - 1, $bg);
        }

        imagealphablending($img, true);
        $this->drawText($img, $size, $fg);

        $out = imagecreatetruecolor($this->o['size'], $this->o['size']);
        imagealphablending($out, false);
        imagesavealpha($out, true);
        imagecopyresampled($out, $img, 0, 0, 0, 0, $this->o['size'], $this->o['size'], $size, $size);

        ob_start();
        imagepng($out);

        return ob_get_clean();
    }

    private function drawText(GdImage $img, int $size, int $color): void
    {
        $font = __DIR__.'/../resources/fonts/'.($this->o['bold'] ? 'DejaVuSans-Bold.ttf' : 'DejaVuSans.ttf');
        $text = $this->initials();
        $px = $size * $this->o['font_size'];

        $box = imagettfbbox($px, 0, $font, $text);
        if ($box === false) {
            throw new RuntimeException('Unable to measure avatar text.');
        }

        $width = $box[2] - $box[0];
        $height = $box[1] - $box[7];
        $x = (int) round(($size - $width) / 2 - $box[0]);
        $y = (int) round(($size - $height) / 2 - $box[7]);

        imagettftext($img, $px, 0, $x, $y, $color, $font, $text);
    }

    private function allocate(GdImage $img, string $hex): int
    {
        [$r, $g, $b] = sscanf($hex, '%02x%02x%02x');

        return imagecolorallocate($img, $r, $g, $b);
    }

    private function resolveBackground(string $value, string $default): string
    {
        if (strtolower($value) === 'random') {
            // Deterministic per name so a user's avatar stays the same across requests.
            return $this->hslToHex(crc32(mb_strtolower($this->name)) % 360, 0.55, 0.42);
        }

        return $this->hex($value, $default === 'random' ? '0d8abc' : $default);
    }

    private function hex(string $value, string $fallback): string
    {
        $value = strtolower(ltrim($value, '#'));

        if (preg_match('/^[0-9a-f]{3}$/', $value)) {
            $value = $value[0].$value[0].$value[1].$value[1].$value[2].$value[2];
        }

        return preg_match('/^[0-9a-f]{6}$/', $value) ? $value : strtolower(ltrim($fallback, '#'));
    }

    private function hslToHex(int $h, float $s, float $l): string
    {
        $c = (1 - abs(2 * $l - 1)) * $s;
        $x = $c * (1 - abs(fmod($h / 60, 2) - 1));
        $m = $l - $c / 2;
        [$r, $g, $b] = match (intdiv($h, 60)) {
            0 => [$c, $x, 0],
            1 => [$x, $c, 0],
            2 => [0, $c, $x],
            3 => [0, $x, $c],
            4 => [$x, 0, $c],
            default => [$c, 0, $x],
        };

        return sprintf('%02x%02x%02x', ($r + $m) * 255, ($g + $m) * 255, ($b + $m) * 255);
    }
}
