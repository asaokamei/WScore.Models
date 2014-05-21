<?php
namespace tests\relationTests\BlogModels;

use Illuminate\Database\Eloquent\Model;

class AuthorAR extends Model
{
    protected $table = 'author';
    
    protected $primaryKey = 'author_id';

    protected $fillable = [
        'status', 'password', 'gender', 'name', 'birth_date', 'email'
    ];

}