<?php

namespace App\Jobs;

use App\Models\Document;
use App\Models\DocumentGroup;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use ZipArchive;

class ZipDocumentGroup implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public DocumentGroup $documentGroup;

    /** @var int $documentType
     * Ο τύπος των αρχείων που θα συμπιέσουμε. Αποδεκτές τιμές είναι οι
     * Document::WithQR και Document::WithQRAndSignature
     */
    public int $documentType;

    public $backoff = 2;

    public $tries = 3;

    /**
     * Create a new job instance.
     */
    public function __construct(DocumentGroup $documentGroup, int $type = Document::WithQR)
    {
        $this->documentGroup = $documentGroup;
        $this->documentType = $type;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Σταμάτησε εγκαίρως αν δεν αναγνωρίζουμε τον τύπο των εγγράφων
        if (!in_array($this->documentType, [Document::WithQR, Document::WithQRAndSignature])) {
            $this->documentGroup->job_status = DocumentGroup::JobFailed;
            $this->documentGroup->job_status_text = 'Άκυρος τύπος εγγράφων για συμπίεση';
            $this->documentGroup->save();
            $this->fail('Άκυρος τύπος εγγράφων για συμπίεση');
            return;
        }

        $documentDir = Document::WithQR ? 'qr' : 'signed';

        $this->documentGroup->job_status_text = 'Συμπίεση αρχείων';
        $this->documentGroup->save();

        $zip = new ZipArchive();
        $filename = storage_path('app')."/{$this->documentGroup->id}/{$documentDir}/{$this->documentGroup->id}.zip";

        if (! $zip->open($filename, ZipArchive::CREATE | ZipArchive::OVERWRITE)) {
            $message = "Αποτυχία συμπίεσης! {$zip->getStatusString()}";
            $this->documentGroup->job_status = DocumentGroup::JobFailed;
            $this->documentGroup->job_status_text = $message;
            $this->documentGroup->save();

            $this->fail($message);
            return;
        }

        foreach ($this->documentGroup->documents()->get() as $document) {
            $document_filename = storage_path('app')."/{$this->documentGroup->id}/{$documentDir}/{$document->id}.pdf";
            logger('Zip: Προσθήκη αρχείου: '. $document_filename);
            if (! $zip->addFile($document_filename, "{$document->filename}")) {
                $message = "Αποτυχία συμπίεσης αρχείου {$document->id}.pdf! {$zip->getStatusString()}";
                $this->documentGroup->job_status = DocumentGroup::JobFailed;
                $this->documentGroup->job_status_text = $message;
                $this->documentGroup->save();

                $this->fail($message);
                return;
            }
        }

        if (! $zip->close()) {
            $message = "Αποτυχία συμπίεσης! {$zip->getStatusString()}";
            $this->documentGroup->job_status = DocumentGroup::JobFailed;
            $this->documentGroup->job_status_text = $message;
            $this->documentGroup->save();

            $this->fail($message);
            return;
        }

        $this->documentGroup->job_status = DocumentGroup::JobFinished;
        $this->documentGroup->job_status_text = 'Ολοκληρώθηκε';

        // Μην αλλάζεις το βήμα αν έχει ήδη ολοκληρωθεί η διαδικασία
        if ($this->documentGroup->step < 3) {
            $this->documentGroup->step = 3;
        }

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
