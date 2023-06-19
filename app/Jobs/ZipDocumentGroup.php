<?php

namespace App\Jobs;

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
        $this->documentGroup->job_status_text = 'Συμπίεση αρχείων';
        $this->documentGroup->save();

        $zip = new ZipArchive();
        $filename = storage_path()."/{$this->documentGroup->id}/{$this->documentGroup->id}.zip";

        if (! $zip->open($filename, ZipArchive::CREATE | ZipArchive::OVERWRITE)) {
            $message = "Αποτυχία συμπίεσης! {$zip->getStatusString()}";
            $this->documentGroup->job_status = DocumentGroup::JobFailed;
            $this->documentGroup->job_status_text = $message;
            $this->documentGroup->save();

            $this->fail($message);
        }

        foreach ($this->documentGroup->documents()->get() as $document) {
            $document_filename = storage_path()."/{$this->documentGroup->id}/qr/{$document->id}.pdf";
            if (! $zip->addFile($document_filename)) {
                $message = "Αποτυχία συμπίεσης αρχείου {$document->id}.pdf! {$zip->getStatusString()}";
                $this->documentGroup->job_status = DocumentGroup::JobFailed;
                $this->documentGroup->job_status_text = $message;
                $this->documentGroup->save();

                $this->fail($message);
            }
        }

        if (! $zip->close()) {
            $message = "Αποτυχία συμπίεσης! {$zip->getStatusString()}";
            $this->documentGroup->job_status = DocumentGroup::JobFailed;
            $this->documentGroup->job_status_text = $message;
            $this->documentGroup->save();

            $this->fail($message);
        }

        $this->documentGroup->job_status = DocumentGroup::JobFinished;
        $this->documentGroup->job_status_text = 'Ολοκληρώθηκε';
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
