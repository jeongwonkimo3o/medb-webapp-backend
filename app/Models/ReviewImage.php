<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReviewImage extends Model
{
    use HasFactory;

    protected $primaryKey = 'image_id';

    protected $fillable = [
        'image_url',
    ];

    public function review()
    {
        return $this->belongsTo(Review::class, 'review_id', 'review_id');
    }
}
