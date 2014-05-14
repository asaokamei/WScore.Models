<?php
namespace tests\DaoTest;

require_once( dirname( dirname( __DIR__ ) ) . '/autoload.php' );

require_once( __DIR__.'/StaticDao.php' );

class DaoTest extends \PHPUnit_Framework_TestCase
{
    public $dao;

    function test0()
    {
        $dao = StaticDao::_();
        $this->assertEquals( 'tests\DaoTest\DaoTest', get_class( $dao ) );
    }
}