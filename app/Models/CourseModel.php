<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class CourseModel extends Model
{
    use HasFactory;

    protected $table = 'courses';

    protected $fillable = [
        'type',
        'subject',
        'room_id',
        'day',
        'time_start',
        'time_end',
        'block',
        'year',
        'semester',
        'is_active'
    ];

    // Automatically save the 'day' in json_encode and decodes it when retrieved.
    protected function day(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => json_decode($value, true),
            set: fn ($value) => json_encode($value),
        );
    }
}
