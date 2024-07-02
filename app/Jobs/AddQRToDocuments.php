<?php

namespace App\Jobs;

use App\Models\Document;
use App\Models\DocumentGroup;
use App\Models\Settings;
use App\Services\ImageService;
use chillerlan\QRCode\Output\QROutputInterface;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\URL;

class AddQRToDocuments implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $documentGroup;

    public $backoff = 2;

    public $timeout = 300;

    public $tries = 3;

    /**
     * Create a new job instance.
     */
    public function __construct(DocumentGroup $documentGroup)
    {
        $this->documentGroup = $documentGroup;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $settings = Settings::all()->first();

        $documents = $this->documentGroup->documents()->get();

        $this->documentGroup->job_status = DocumentGroup::JobInProgress;
        $this->documentGroup->job_status_text = "";
        $this->documentGroup->save();

        foreach ($documents as $index=>$document) {
            $filename = storage_path('app')."/{$this->documentGroup->id}/{$document->id}.pdf";
            $output_filename = storage_path('app')."/{$this->documentGroup->id}/qr/{$document->id}.pdf";
            logger("Adding QR to {$filename}");
            $output = null;
            $document_link = URL::to("/validateQR/document/{$document->id}");

            // Δημιούργησε τον φάκελο qr μέσα στον φάκελο της ομάδας γιατί τον χρειαζόμαστε
            if (!file_exists(storage_path('app') . "/{$this->documentGroup->id}/qr")) {
                logger("Create qr folder");
                mkdir(storage_path('app') . "/{$this->documentGroup->id}/qr");
            }

            $this->createImage($document, $document_link);

            $command = [
                "/usr/bin/qpdfImageEmbed",
                "-i",
                $filename,
                "-o",
                $output_filename,
                "--img-x",
                $settings->img_x,
                "--img-y",
                $settings->img_y,
                "--img-scale",
                $settings->img_scale,
                "--img-link-to",
                $document_link,
                "-s",
                storage_path('app') . "/{$this->documentGroup->id}/qr/{$document->id}.png",
            ];

            logger($command);

            $result = Process::run($command, $output);

            logger(implode(' ', $command));
            $return_value = $result->exitCode();

            if ($return_value != null && $return_value != 0) {
                $message = "Αποτυχία αποθήκευσης αρχείου '{$this->documentGroup->id}/qr/{$document->id}.pdf'";
                logger($output);
                logger($return_value);
                $this->documentGroup->job_status = DocumentGroup::JobFailed;
                $this->documentGroup->job_status_text = $message;
                $this->documentGroup->save();

                logger($message);
                $this->fail($message);
                return;
            }

            unlink(storage_path('app') . "/{$this->documentGroup->id}/qr/{$document->id}.png");

            $document->state = Document::WithQR;
            $document->save();

            $this->documentGroup->job_status_text = sprintf("%.2f", $index / $documents->count() * 100) . "%";
            $this->documentGroup->save();
        }

        $this->documentGroup->job_status_text = 'Ολοκληρώθηκε η δημιουργία QR';
        $this->documentGroup->save();
    }

    /**
     * The unique ID of the job.
     *
     * @return string
     */
    public function uniqueId()
    {
        return $this->documentGroup->id;
    }

    public function createImage(Document $document, string $document_link): void
    {
        // Ετοίμασε το κείμενο που θα εισαχθεί στο PDF
        $service = new ImageService();
        $imageText = $service->create(600, 120)
            ->annotate(5, 30, "Για την επιβεβαίωση της γνησιότητας του εγγράφου", [ 'fontsize' => 16 ])
            ->annotate(5, 52, "μπορείτε να σαρώσετε τον κωδικό στα αριστερά ή να κάνετε κλικ επάνω του", [ 'fontsize' => 16 ])
            ->annotate(5, 76, "ή να εισάγετε τον κωδικό {$document->id} στη σελίδα", [ 'fontsize' => 16 ])
            ->annotate(5, 100, URL::to("/"),
            [ 'fontsize' => 16, 'fontFamily' => 'Courier' ])
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
        $combined->transparentPaintImage("white", 0, 0, false);
        $combined->writeImage(storage_path('app') . "/{$this->documentGroup->id}/qr/{$document->id}.png");
    }
}
