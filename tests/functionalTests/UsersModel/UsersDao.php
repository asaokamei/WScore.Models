<?php
namespace WScore\functionalTests\UsersModel;

use WScore\DbGateway\Converter;
use WScore\DbGateway\Dao;

class UsersDao extends Dao
{
    /**
     * @param $db
     * @return static
     */
    public static function getInstance( $db )
    {
        return new static( $db, new Converter() );
    }
}