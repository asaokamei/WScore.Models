<?php
namespace WScore\functionalTests\UsersModel;

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
    
    /**
     * @param $db
     * @return static
     */
    public static function getInstance( $db )
    {
        /** @var DaoEntity $dao */
        $dao = new static( $db );
        $dao->setConverter( new UsersConverter() );
        return $dao;
    }
}