<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class MedicalRecord extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }

    public function prescriptionDetails()
    {
        return $this->hasMany(PrescriptionDetail::class);
    }
}