<?php

namespace TestQ\Avatars;

use Stringable;

class Avatar implements Stringable
{
    private array $options;

    public function __construct(private string $name, array $options = [])
    {
        $this->options = array_replace(config('avatars.defaults'), $options);
    }

    public static function make(string $name, array $options = []): static
    {
        return new static($name, $options);
    }

    public function size(int $size): static
    {
        return $this->set('size', $size);
    }

    public function background(string $color): static
    {
        return $this->set('background', $color);
    }

    public function color(string $color): static
    {
        return $this->set('color', $color);
    }

    public function length(int $length): static
    {
        return $this->set('length', $length);
    }

    public function fontSize(float $ratio): static
    {
        return $this->set('font_size', $ratio);
    }

    public function rounded(bool $rounded = true): static
    {
        return $this->set('rounded', $rounded);
    }

    public function bold(bool $bold = true): static
    {
        return $this->set('bold', $bold);
    }

    public function uppercase(bool $uppercase = true): static
    {
        return $this->set('uppercase', $uppercase);
    }

    public function format(string $format): static
    {
        return $this->set('format', $format);
    }

    public function svg(): string
    {
        return (new Renderer($this->name, $this->options))->svg();
    }

    public function png(): string
    {
        return (new Renderer($this->name, $this->options))->png();
    }

    public function render(): string
    {
        return $this->options['format'] === 'png' ? $this->png() : $this->svg();
    }

    public function mime(): string
    {
        return $this->options['format'] === 'png' ? 'image/png' : 'image/svg+xml';
    }

    public function toDataUri(): string
    {
        return 'data:'.$this->mime().';base64,'.base64_encode($this->render());
    }

    /** Absolute URL of the package endpoint (ui-avatars.com compatible query string). */
    public function toUrl(): string
    {
        $query = [
            'name' => $this->name,
            'size' => $this->options['size'],
            'background' => $this->options['background'],
            'color' => $this->options['color'],
            'length' => $this->options['length'],
            'font-size' => $this->options['font_size'],
            'rounded' => $this->options['rounded'] ? 'true' : 'false',
            'bold' => $this->options['bold'] ? 'true' : 'false',
            'uppercase' => $this->options['uppercase'] ? 'true' : 'false',
            'format' => $this->options['format'],
        ];

        return url(config('avatars.path')).'?'.http_build_query($query);
    }

    public function __toString(): string
    {
        return $this->toUrl();
    }

    private function set(string $key, mixed $value): static
    {
        $this->options[$key] = $value;

        return $this;
    }
}
