<?php

namespace App\Jobs;

use App\Models\Document;
use App\Models\DocumentGroup;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use setasign\Fpdi\Fpdi as Fpdi;

class AddQRToDocuments implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $documentGroup;

    public $backoff = 2;

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
        $documents = $this->documentGroup->documents()->get();

        foreach ($documents as $document) {
            $pdf = new Fpdi();

            $filename = "{$this->documentGroup->id}/{$document->id}.pdf";
            logger("Reading file '{$this->documentGroup->id}/{$document->id}.pdf'");
            $pages_count = $pdf->setSourceFile(Storage::readStream($filename));

            // Αντέγραψε κάθε σελίδα
            for ($i = 1; $i <= $pages_count; $i++) {
                $tplIdx = $pdf->importPage($i);
                $size = $pdf->getTemplateSize($tplIdx);
                $width = $size['width'];
                $height = $size['height'];

                if ($height > $width) { // Portrait
                    $pdf->AddPage('P');
                } else { // Landscape
                    $pdf->AddPage('L');
                }

                $pdf->useTemplate($tplIdx, 0, 0);

                if ($i === 1) {
                    $options = new QROptions([
                        'version' => 6,
                        'outputType' => QRCode::OUTPUT_IMAGE_PNG,
                        'eccLevel' => QRCode::ECC_M,
                        'scale' => 2,
                        'imageBase64' => true,
                    ]);

                    $pdf->Image(
                        (new QRCode($options))->render("https://srv-dide-v.thess.sch.gr/evaluateQR/evaluate/{$document->id}"),
                        $width - 30,
                        5,
                        0,
                        0,
                        'PNG',
                        "https://srv-dide-v.thess.sch.gr/evaluateQR/evaluate/{$document->id}"
                    );
                }
            }

            $documentWithQR = $pdf->Output('S');
            if (! Storage::put("{$this->documentGroup->id}/qr/{$document->id}.pdf", $documentWithQR)) {
                $message = "Αποτυχία αποθήκευσης αρχείου '{$this->documentGroup->id}/qr/{$document->id}.pdf'";
                $this->documentGroup->job_status = DocumentGroup::JobFailed;
                $this->documentGroup->job_status_text = $message;
                $this->documentGroup->save();

                logger($message);
                $this->fail($message);
            }

            $document->state = Document::WithQR;
            $document->save();
            usleep(250000);
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
}
