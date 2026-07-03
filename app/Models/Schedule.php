<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Schedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'doctor_id',
        'day_of_week',
        'shift',
        'max_patients_per_slot',
        'is_active',
    ];

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }
}
