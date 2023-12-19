<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    use HasFactory;

    public function review()
    {
        return $this->belongsTo(Review::class, 'review_id', 'review_id');
    }
}
