<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $fillable = [
        'title',
        'body',
        'is_completed',
    ];

    protected $casts = [
        'is_completed' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($task) {
            $task->user_id = auth()->id();
        });
    }

    public function scopeForUser($query)
    {
        return $query->where('user_id', auth()->id());
    }
}
