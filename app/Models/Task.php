<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

     protected $fillable = [
        'title',
        'description',
        'status',
        'progress',
        'image_path'
    ];

    /**
     * The user that owns the task.
     */
    public function users()
    {
        return $this->belongsToMany(User::class)->withTimestamps();
    }

}
