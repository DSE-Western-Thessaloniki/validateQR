<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentExtraState extends Model
{
    use HasFactory;

    protected $fillable = [
        'document_id',
        'extra_state',
        'extra_state_text',
    ];

    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class);
    }
}
