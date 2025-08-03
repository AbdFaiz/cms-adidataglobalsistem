<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailAttachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'email_id', 'filename', 'filepath', 'mime_type', 'size'
    ];

    public function email()
    {
        return $this->belongsTo(Email::class);
    }
    
    public function getIconAttribute()
    {
        $extension = pathinfo($this->filename, PATHINFO_EXTENSION);
        
        switch(strtolower($extension)) {
            case 'pdf': return 'pdf';
            case 'doc':
            case 'docx': return 'word';
            case 'xls':
            case 'xlsx': return 'excel';
            case 'ppt':
            case 'pptx': return 'powerpoint';
            case 'jpg':
            case 'jpeg':
            case 'png':
            case 'gif': return 'image';
            case 'zip':
            case 'rar': return 'archive';
            default: return 'alt';
        }
    }
}
