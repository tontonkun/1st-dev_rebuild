<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rest extends Model
{
    use HasFactory;

    protected $fillable = [
        'work_id',
        'break_start_time',
        'break_end_time'
    ];

    public function user()
    {
        return $this->belongsTo(Work::class);
    }
}
