<?php
namespace tests\relationTests\BlogModels;

use WScore\Models\Dao\Relation;
use WScore\Models\DaoEntity;

class Author_RoleDao extends DaoEntity
{
    protected $table = 'blog_author_role';

    protected $primaryKey = 'author_role_id';

    protected $columns = [
        'author_id', 'role_id',
    ];

    protected $timeStamps = [
        'created_at', 'updated_at'
    ];
}