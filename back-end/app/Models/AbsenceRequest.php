<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AbsenceRequest extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];
    public $timestamps = false;

    public function schedule()
    {
        return $this->belongsTo(Schedule::class);
    }
}
