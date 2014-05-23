<?php
namespace tests\relationTests\BlogModels;

use Illuminate\Database\Capsule\Manager;
use WScore\Models\Dao\Relation;
use WScore\Models\DaoEntity;

class BlogDao extends DaoEntity
{
    protected $table = 'blog_blog';

    protected $primaryKey = 'blog_id';

    protected $columns = [
        'status', 'author_id', 'title', 'content',
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
        $relation->belongsTo( 'author',   'AuthorDao' );
        $relation->hasMany(   'comments', 'CommentDao' );
    }
}