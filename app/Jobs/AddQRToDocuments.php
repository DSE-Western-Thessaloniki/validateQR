<?php

namespace App\Jobs;

use App\Models\Document;
use App\Models\DocumentGroup;
use App\Models\Settings;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Process;

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
        $settings = Settings::all()->first();

        $documents = $this->documentGroup->documents()->get();

        $this->documentGroup->job_status = DocumentGroup::JobInProgress;
        $this->documentGroup->job_status_text = "";
        $this->documentGroup->save();

        foreach ($documents as $index=>$document) {
            $filename = storage_path()."/app/{$this->documentGroup->id}/{$document->id}.pdf";
            $output_filename = storage_path()."/app/{$this->documentGroup->id}/qr/{$document->id}.pdf";
            logger("Adding QR to {$filename}");
            $output = null;
            $document_link = "https://srv-dide-v.thess.sch.gr/evaluateQR/evaluate/{$document->id}";

            // Δημιούργησε τον φάκελο qr μέσα στον φάκελο της ομάδας γιατί τον χρειαζόμαστε
            if (!file_exists(storage_path(). "/app/{$this->documentGroup->id}/qr")) {
                logger("Create qr folder");
                mkdir(storage_path(). "/app/{$this->documentGroup->id}/qr");
            }

            $command = [
                "/usr/bin/qpdfImageEmbed",
                "-i",
                $filename,
                "--qr",
                $document_link,
                "--link",
                "--qr-side",
                $settings->qr_side,
                "--qr-scale",
                $settings->qr_scale,
                "--qr-top-margin",
                $settings->qr_top_margin,
                "--qr-side-margin",
                $settings->qr_side_margin,
                "-o",
                $output_filename,
            ];

            // Έλεγξε αν έχει ρυθμιστεί εικόνα για εισαγωγή
            // και πέρασε τις κατάλληλες ρυθμίσεις
            if ($settings->img_filename !== "") {
                $command[] = [
                    "--img-side",
                    $settings->img_side,
                    "--img-scale",
                    $settings->img_scale,
                    "--img-top-margin",
                    $settings->img_top_margin,
                    "--img-side-margin",
                    $settings->img_side_margin,
                    "-s",
                    storage_path() . "/$settings->img_filename",
                ];
            }

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

            $document->state = Document::WithQR;
            $document->save();

            $this->documentGroup->job_status_text = sprintf("%.2f", $index / $documents->count() * 100) . "%";
            $this->documentGroup->save();

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
