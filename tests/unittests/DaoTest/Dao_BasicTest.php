<?php
namespace tests\DaoTest;

use WScore\DbGateway\Dao;

require_once( dirname( dirname( __DIR__ ) ) . '/autoload.php' );

require_once( __DIR__.'/StaticDao.php' );
require_once( __DIR__.'/MoreDao.php' );

class DaoTest extends \PHPUnit_Framework_TestCase
{
    function setup()
    {
        Dao::_cleanUp();
    }

    function tearDown()
    {
        Dao::_cleanUp();
    }

    function test0()
    {
        $dao = StaticDao::getInstance();
        $this->assertEquals( 'tests\DaoTest\StaticDao', get_class( $dao ) );
        $dao = Dao::StaticDao();
        $this->assertEquals( 'tests\DaoTest\StaticDao', get_class( $dao ) );
    }

    /**
     * @test
     */
    function Dao_returns_table_name()
    {
        StaticDao::getInstance();
        MoreDao::getInstance();
        $this->assertEquals( 'tests\DaoTest\StaticDao', get_class( Dao::StaticDao() ) );
        $this->assertEquals( 'tests\DaoTest\MoreDao', get_class( Dao::more() ) );
    }

    /**
     * @test
     * @expectedException \RuntimeException
     */
    function calling_non_existence_dao_throws_exception()
    {
        Dao::notExist();
    }

    /**
     * @test
     */
    function callMe_calls_callMe_methods_of_object()
    {
        StaticDao::getInstance();
        $called = Dao::StaticDao()->callMe();
    }
}