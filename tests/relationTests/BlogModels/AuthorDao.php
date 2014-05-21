<?php
namespace tests\relationTests\BlogModels;

use Illuminate\Database\Capsule\Manager;
use WScore\Models\Dao\Converter;
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
     * @param $db
     * @return static
     */
    public static function getInstance( $db )
    {
        /** @var DaoEntity $dao */
        $dao = new static( $db );
        $dao->setConverter( new Converter() );
        return $dao;
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