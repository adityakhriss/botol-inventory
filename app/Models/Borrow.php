<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Borrow extends Model
{
    protected $fillable = ['borrower_name','borrowed_at','returned_at','handled_by'];

    protected $casts = [
        'borrowed_at' => 'datetime',
        'returned_at' => 'datetime',
    ];

    public function items()
{
    return $this->hasMany(\App\Models\BorrowItem::class);
}

public function handledByUser()
{
    return $this->belongsTo(\App\Models\User::class, 'handled_by');
}


}

