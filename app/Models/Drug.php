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
}
