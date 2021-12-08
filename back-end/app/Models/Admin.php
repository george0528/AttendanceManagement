<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User;
use Illuminate\Notifications\Notifiable;

class Admin extends User
{
    use Notifiable, HasFactory, SoftDeletes;
    protected $dates = ['deleted_at'];
    protected $guard = 'admin';
    protected $guarded = ['id'];
}
