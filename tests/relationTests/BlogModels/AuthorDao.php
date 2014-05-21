<?php
namespace tests\relationTests\BlogModels;

use Illuminate\Database\Capsule\Manager;
use WScore\Models\Dao\Relation;
use WScore\Models\DaoEntity;

class AuthorDao extends DaoEntity
{
    protected $table = 'blog_author';

    protected $primaryKey = 'author_id';
    
    protected $columns = [
        'status', 'name', 'gender', 'name', 'birth_date', 'email',
    ];

    protected $timeStamps = [
        'created_at', 'updated_at'
    ];

    protected $entityClass = '\tests\relationTests\BlogModels\Author';

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
    public function muteStatusAttribute( $value )
    {
        return new AuthorStatus($value);
    }

    /**
     * @param string $value
     * @return AuthorGender
     */
    public function muteGenderAttribute( $value )
    {
        return new AuthorGender($value);
    }
}