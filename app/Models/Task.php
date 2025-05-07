<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'body',
        'is_completed',
        'user_id', // Add user_id to fillable
    ];

    protected $casts = [
        'is_completed' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($task) {
            // Only set user_id if it is not already provided
            if (!$task->user_id) {
                $task->user_id = auth()->id();
            }
        });
    }

    public function scopeForUser($query)
    {
        return $query->where('user_id', auth()->id());
    }
}
