<?php
namespace tests\DaoTest;

use WScore\Models\Dao;

class StaticDao
{
    public static function getInstance()
    {
        $self = new self(null);
        Dao::_setDaoObject( $self );
        return $self;
    }

    /**
     * @return string
     */
    public function callMe() {
        return 'you called me up';
    }
}