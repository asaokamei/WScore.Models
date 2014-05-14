<?php
namespace tests\DaoTest;

use WScore\DbGateway\Dao;

class MoreDao
{
    public static function getInstance()
    {
        $self = new self(null);
        Dao::_setDaoObject( $self, 'more' );
        return $self;
    }
}