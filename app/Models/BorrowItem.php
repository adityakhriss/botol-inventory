<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BorrowItem extends Model
{
    protected $fillable = ['borrow_id','bottle_id','returned_at','handled_by'];

    protected $casts = [
        'returned_at' => 'datetime',
    ];

    public function bottle() { return $this->belongsTo(Bottle::class); }
    public function borrow() { return $this->belongsTo(Borrow::class); }
}