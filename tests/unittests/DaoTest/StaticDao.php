<?php
namespace tests\DaoTest;

use WScore\DbGateway\Dao;

class StaticDao extends Dao
{
    public static function getInstance($db)
    {
        return new self($db);
    }
}