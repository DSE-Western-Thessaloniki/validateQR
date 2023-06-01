<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Document extends Model
{
    use HasFactory;
    use HasUuids;

    protected $fillable = [
        'document_group_id',
        'filename',
        'state'
    ];

    const InitialState = 0;
    const WithQR = 1;
    const WithQRAndSignature = 2;

    public function documentGroup(): BelongsTo {
        return $this->belongsTo(DocumentGroup::class);
    }
}
