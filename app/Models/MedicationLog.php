<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicationLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_seq', 'end_date'
    ];

    public function drug()
    {
        return $this->belongsTo(Drug::class);
    }
}
