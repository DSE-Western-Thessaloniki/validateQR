<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Settings extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'qr_side',
        'qr_top_margin',
        'qr_side_margin',
        'qr_scale',
        'img_side',
        'img_top_margin',
        'img_side_margin',
        'img_scale',
        'img_filename',
    ];
}
