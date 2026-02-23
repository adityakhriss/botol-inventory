<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bottle extends Model
{
    protected $fillable = ['code','type','status'];

    public function borrowItems() {
        return $this->hasMany(BorrowItem::class);
    }

    public function activeBorrowItem() {
        return $this->hasOne(BorrowItem::class)->whereNull('returned_at');
    }
}