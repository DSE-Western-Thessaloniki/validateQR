<?php

namespace App\Services;

use App\Models\Document;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use Illuminate\Support\Facades\URL;

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

    public function createQRImage(Document $document, string $document_link): void
    {
        // Ετοίμασε το κείμενο που θα εισαχθεί στο PDF
        $imageText = $this->create(600, 120)
            ->annotate(5, 30, 'Για την επιβεβαίωση της γνησιότητας του εγγράφου', ['fontsize' => 16])
            ->annotate(5, 52, 'μπορείτε να σαρώσετε τον κωδικό στα αριστερά ή να κάνετε κλικ επάνω του', ['fontsize' => 16])
            ->annotate(5, 76, "ή να εισάγετε τον κωδικό {$document->id} στη σελίδα", ['fontsize' => 16])
            ->annotate(5, 100, URL::to('/'),
                ['fontsize' => 16, 'fontFamily' => 'Courier'])
            ->resource();

        // Ετοίμασε το QRCode
        $options = new QROptions();
        $options->outputType = QRCode::OUTPUT_IMAGICK;
        $options->eccLevel = QRCode::ECC_M;
        $options->imagickFormat = 'png';
        $options->pngCompression = 9;
        $options->scale = 3;
        $options->returnResource = true;

        /** @var \Imagick $imageQR */
        $imageQR = (new QRCode($options))->render($document_link);

        // Συνδύασε τις δύο εικόνες
        $imageQR->addImage($imageText);
        $imageQR->resetIterator();
        $combined = $imageQR->appendImages(false);
        $combined->transparentPaintImage('white', 0, 0, false);
        $combined->writeImage(storage_path('app')."/{$document->documentGroup->id}/qr/{$document->id}.png");
    }
}
