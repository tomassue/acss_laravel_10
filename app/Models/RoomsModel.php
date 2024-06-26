<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoomsModel extends Model
{
    use HasFactory;

    protected $table = 'rooms';

    protected $fillable = [
        'name'
    ];
}
