<?php
namespace tests\relationTests\BlogModels;

use Illuminate\Database\Capsule\Manager;
use WScore\Models\Dao\Relation;
use WScore\Models\DaoEntity;

class RoleDao extends DaoEntity
{
    protected $table = 'blog_role';

    protected $primaryKey = 'role_id';

    protected $columns = [
        'status', 'name',
    ];

    protected $timeStamps = [
        'created_at', 'updated_at'
    ];

    /**
     * @param Manager $db
     */
    public function __construct( $db )
    {
        parent::__construct( $db );
    }

    /**
     * @param Relation $relation
     */
    public function setRelation( $relation )
    {
        parent::setRelation($relation);
        $relation->hasJoin(   'authors',   'AuthorDao' );
    }
}