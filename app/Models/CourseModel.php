<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseModel extends Model
{
    use HasFactory;

    protected $table = 'course';

    protected $fillable = [
        'type',
        'subject',
        'room_id',
        'day',
        'time_start',
        'time_end',
        'block',
        'year',
        'semester'
    ];
}
