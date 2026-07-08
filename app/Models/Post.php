<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'slug', 'content', 'image', 'is_published',
        'meta_title', 'meta_description', 'meta_keywords', 'excerpt',
        'read_time', 'views_count'
    ];
}
