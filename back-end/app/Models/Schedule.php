<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Schedule extends Model
{
    use HasFactory, SoftDeletes;
    protected $guarded = ['id'];
    public $timestamps = false;

    public function absence_request()
    {
        return $this->hasOne(AbsenceRequest::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
