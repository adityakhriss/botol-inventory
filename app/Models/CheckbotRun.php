<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CheckbotRun extends Model
{
    protected $fillable = [
        'analyst_name',
        'tested_at',
        'sampling_rate',
        'created_by',
    ];

    protected $casts = [
        'tested_at' => 'date',
    ];

    public function items()
    {
        return $this->hasMany(CheckbotItem::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
