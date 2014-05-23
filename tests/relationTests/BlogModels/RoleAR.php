<?php
namespace tests\relationTests\BlogModels;

use Illuminate\Database\Eloquent\Model;

class RoleAR extends Model
{
    protected $table = 'blog_role';
    
    protected $primaryKey = 'role_id';

    protected $fillable = [
        'status', 'name'
    ];

}