<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'drug_name', 'content', 'drug_id', 'rating',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function images()
    {
        return $this->hasMany(ReviewImage::class, 'review_id', 'id');
    }

    public function drug()
    {
        return $this->belongsTo(Drug::class, 'drug_id', 'id');
    }
}
