<?php

namespace App\Models;

use App\Services\ImageService;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\URL;

class Document extends Model
{
    use HasFactory;
    use HasUlids;

    protected $fillable = [
        'document_group_id',
        'filename',
        'state',
    ];

    const InitialState = 0;

    const WithQR = 1;

    const WithQRAndSignature = 2;

    const ExtraStateCancelled = 1;

    const ExtraStateReplaced = 2;

    public function documentGroup(): BelongsTo
    {
        return $this->belongsTo(DocumentGroup::class);
    }

    public function extraState(): HasOne
    {
        return $this->hasOne(DocumentExtraState::class);
    }

    public function addQR(): array
    {
        $settings = Settings::all()->first();

        $filename = storage_path('app')."/{$this->documentGroup->id}/{$this->id}.pdf";
        $output_filename = storage_path('app')."/{$this->documentGroup->id}/qr/{$this->id}.pdf";
        logger("Adding QR to {$filename}");
        $output = null;
        $document_link = URL::to("/document/{$this->id}");

        // Δημιούργησε τον φάκελο qr μέσα στον φάκελο της ομάδας γιατί τον χρειαζόμαστε
        if (! file_exists(storage_path('app')."/{$this->documentGroup->id}/qr")) {
            logger('Create qr folder');
            mkdir(storage_path('app')."/{$this->documentGroup->id}/qr");
        }

        (new ImageService)->createQRImage($this, $document_link);

        $command = [
            '/usr/bin/qpdfImageEmbed',
            '-i',
            $filename,
            '-o',
            $output_filename,
            '--img-x',
            $settings->img_x,
            '--img-y',
            $settings->img_y,
            '--img-scale',
            $settings->img_scale,
            '--img-link-to',
            $document_link,
            '-s',
            storage_path('app')."/{$this->documentGroup->id}/qr/{$this->id}.png",
        ];

        logger($command);

        $result = Process::run($command, $output);

        logger(implode(' ', $command));
        $return_value = $result->exitCode();

        unlink(storage_path('app')."/{$this->documentGroup->id}/qr/{$this->id}.png");

        if ($return_value != null && $return_value != 0) {
            return [
                'ok' => false,
                'return_value' => $return_value,
                'output' => $output
            ];
        }

        // Αν το έγγραφο είναι στην αρχική του κατάσταση σημείωσε ότι βάλαμε QR
        if ($this->state === Document::InitialState) {
            $this->state = Document::WithQR;
        }

        $this->save();

        return [
            'ok' => true,
        ];
    }
}
