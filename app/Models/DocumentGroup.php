<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DocumentGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'step',
        'published',
    ];

    const JobNotStarted = 0;

    const JobInProgress = 1;

    const JobFinished = 2;

    const JobFailed = 3;

    const JobAborted = 4;

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }
}
