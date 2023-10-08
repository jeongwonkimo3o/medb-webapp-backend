<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicationLog extends Model
{
    use HasFactory;

    protected $primaryKey = 'log_id';

    public $timestamps = false;

    protected $fillable = [
        'drug_name',
        'drug_information',
        'start_date',
        'last_date'
    ];
}
