<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $primaryKey = 'review_id';

    public function images()
    {
        return $this->hasMany(ReviewImage::class, 'review_id', 'review_id');
    }

    public function feedbacks()
    {
        return $this->hasMany(Feedback::class, 'review_id', 'review_id');
    }
}
