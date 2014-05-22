<?php
namespace WScore\functionalTests\UsersModel;

use Illuminate\Database\Capsule\Manager;
use WScore\Models\Dao\Converter;
use WScore\Models\Dao\Relation;
use WScore\Models\Dao\TimeStamp;
use WScore\Models\DaoEntity;

require_once( __DIR__.'/UsersConverter.php' );
require_once( __DIR__.'/UserEntity.php' );

class UsersDao extends DaoEntity
{
    protected $table = 'users';
    
    protected $primaryKey = 'user_id';

    protected $columns = [
        'user_id', 'status', 'password', 'gender', 'name', 'birth_date', 'email'
    ];

    protected $entityClass = '\WScore\functionalTests\UsersModel\UserEntity';

    /**
     * @param Manager $db
     * @param TimeStamp|null $stamps
     * @param Converter|null $converter
     * @param Relation|null  $relation
     * @return DaoEntity
     */
    public static function getInstance( $db, $stamps=null, $converter=null, $relation=null )
    {
        /** @var DaoEntity $dao */
        $dao = parent::getInstance($db, null, new UsersConverter() );
//        $dao = new static( $db );
  //      $dao->setConverter( new UsersConverter() );
        return $dao;
    }
}