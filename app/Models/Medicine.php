<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Medicine extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    public function prescriptionDetails()
    {
        return $this->hasMany(PrescriptionDetail::class);
    }
}