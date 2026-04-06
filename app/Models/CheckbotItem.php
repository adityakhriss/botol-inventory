<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CheckbotItem extends Model
{
    protected $fillable = [
        'checkbot_run_id',
        'bottle_id',
        'bottle_code_snapshot',
        'parameter',
        'test_result',
        'status',
    ];

    public function run()
    {
        return $this->belongsTo(CheckbotRun::class, 'checkbot_run_id');
    }

    public function bottle()
    {
        return $this->belongsTo(Bottle::class);
    }
}
