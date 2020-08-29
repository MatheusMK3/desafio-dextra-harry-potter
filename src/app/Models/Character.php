<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Character extends Model
{
    // Fields that can be automatically filled via the fill() function
    public $fillable = ['name', 'role', 'school', 'house', 'patronus'];
}
