<?php
namespace tests\relationTests\BlogModels;

use Illuminate\Database\Eloquent\Model;

class BlogAR extends Model
{
    protected $table = 'blog_Blog';
    
    protected $primaryKey = 'Blog_id';

    protected $fillable = [
        'status', 'author_id', 'title', 'content'
    ];

}