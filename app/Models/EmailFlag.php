<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailFlag extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function email()
    {
        return $this->belongsTo(Email::class);
    }
}
