<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'created_at' => 'datetime:m-d-Y',
        'updated_at' => 'datetime:m-d-Y',
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function comment() {
        return $this->hasMany(Comment::class);
    }

}
