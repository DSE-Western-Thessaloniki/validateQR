<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

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
}
