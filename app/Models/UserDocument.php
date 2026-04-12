<?php

namespace App\Models;

use Database\Factories\UserDocumentFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserDocument extends Model
{
    /** @use HasFactory<UserDocumentFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'file_path',
        'file_name',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
