<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Email extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function attachments()
    {
        return $this->hasMany(EmailAttachment::class);
    }

    public function flags()
    {
        return $this->hasOne(EmailFlag::class);
    }
}
