<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Drug extends Model
{
    use HasFactory;

    protected $fillable = [
        'entp_name', 'item_name', 'item_seq', 'efcy_qesitm', 'use_method_qesitm',
        'atpn_warn_qesitm', 'intrc_qesitm', 'se_qesitm', 'deposit_method_qesitm', 'item_image'
    ];

    public function reviews()
    {
        return $this->hasMany(Review::class, 'drug_id', 'id'); 
    }

    public function medicationLogs()
    {
        return $this->hasMany(MedicationLog::class);
    }
}
