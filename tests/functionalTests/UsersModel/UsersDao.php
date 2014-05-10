<?php
namespace WScore\functionalTests\UsersModel;

use WScore\DbGateway\Dao;

require_once( __DIR__.'/UsersConverter.php' );
require_once( __DIR__.'/UserEntity.php' );

class UsersDao extends Dao
{
    protected $table = 'users';
    
    protected $primaryKey = 'user_id';

    protected $columns = [
        'user_id', 'status', 'gender', 'name', 'birth_date', 'email'
    ];
    
    /**
     * @param $db
     * @return static
     */
    public static function getInstance( $db )
    {
        return new static( $db, new UsersConverter() );
    }
}