<?php

namespace App\Jobs;

use App\Models\DocumentGroup;
use App\Models\Settings;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

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
        $this->documentGroup->job_status_text = '';
        $this->documentGroup->save();

        foreach ($documents as $index => $document) {
            /** @var \App\Models\Document $document */
            $result = $document->addQR();

            if (!$result['ok']) {
                $message = "Αποτυχία αποθήκευσης αρχείου '{$this->documentGroup->id}/qr/{$document->id}.pdf'";
                logger($result['output']);
                logger($result['return_value']);
                $this->documentGroup->job_status = DocumentGroup::JobFailed;
                $this->documentGroup->job_status_text = $message;
                $this->documentGroup->save();

                logger($message);
                $this->fail($message);

                return;
            }

            $this->documentGroup->job_status_text = sprintf('%.2f', $index / $documents->count() * 100).'%';
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
}
