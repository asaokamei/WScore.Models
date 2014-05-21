<?php
namespace Blogs\Model;

use Illuminate\Database\Capsule\Manager;
use WScore\Models\Dao\Relation;
use WScore\Models\DaoEntity;

class AuthorDao extends DaoEntity
{
    protected $table = 'author';

    protected $columns = [
        'status', 'name', 'gender', 'name', 'birth_date', 'email',
    ];

    protected $timeStamps = [
        'created_at', 'updated_at'
    ];

    protected $entityClass = '\Blogs\Model\UserEntity';

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
        $relation->hasMany( 'blogs', 'BlogDao' );
        $relation->hasJoin( 'roles', 'RoleDao' );
        parent::setRelation($relation);
    }

    /**
     * @param int $value
     * @return AuthorStatus
     */
    protected function setStatus( $value )
    {
        return new AuthorStatus($value);
    }

    /**
     * @param string $value
     * @return AuthorGender
     */
    protected function setGender( $value )
    {
        return new AuthorGender($value);
    }
}