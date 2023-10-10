<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $primaryKey = 'review_id';

    protected $fillable = [
        'drug_name',
        'content',
    ];

    protected $hidden = ['feedbacks'];

    public function images()
    {
        return $this->hasMany(ReviewImage::class, 'review_id', 'review_id');
    }

    public function feedbacks()
    {
        return $this->hasMany(Feedback::class, 'review_id', 'review_id');
    }

    public function getLikesCountAttribute()
    {
        return $this->feedbacks->where('feedbacks', 'like')->count();
    }

    public function getDislikesCountAttribute()
    {
        return $this->feedbacks->where('feedbacks', 'dislike')->count();
    }

    protected $appends = ['likes_count', 'dislikes_count'];
}
