<?php

namespace App\Services;

class ImageService
{
    private \Imagick $canvas;

    public function create(int $width = 600, int $height = 150, string $background = "transparent"): ImageService
    {
        $this->canvas = new \Imagick();
        $this->canvas->newImage($width, $height, $background);

        return $this;
    }

    public function annotate(float $x, float $y, string $text, array $options): ImageService
    {
        $fontsize = $options['fontsize'] ?? 14;
        $angle = $options['angle'] ?? 0;
        $fontFamily = $options['fontFamily'] ?? 'Helvetica';

        $draw = new \ImagickDraw();
        $draw->setFontSize($fontsize);
        $draw->setFontFamily($fontFamily);

        $this->canvas->annotateImage($draw, $x, $y, $angle, $text);

        return $this;
    }

    public function echo(string $fileFormat = 'png'): void
    {
        $this->canvas->setImageFormat('png');
        echo $this->canvas;
    }

    public function save(string $filename): void
    {
        $this->canvas->writeImage($filename);
    }

    public function resource(): \Imagick
    {
        return $this->canvas;
    }
}
