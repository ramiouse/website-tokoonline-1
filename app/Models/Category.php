<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use HasFactory;
    // is db using softdeletes
    use SoftDeletes;

    // is field in db
    protected $fillable = ["name","photo","slug"];
    protected $hidden = [
        
    ];
}
