<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ShiftRequest extends Model
{
    use HasFactory, SoftDeletes;
    protected $guarded = ['id'];
    const UPDATED_AT = null;
}
