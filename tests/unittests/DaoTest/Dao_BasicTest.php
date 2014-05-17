<?php
namespace tests\DaoTest;

use WScore\Models\Dao;

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
        $this->assertEquals( 'tests\DaoTest\MoreDao', get_class( Dao::More() ) );
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

    /**
     * @ test
     */
    function static_call_for_callMe_not_working_yet()
    {
        StaticDao::getInstance();
        $this->assertTrue( class_exists( '\StaticDao' ) );
        $this->assertEquals( 'tests\DaoTest\StaticDao', get_class( \StaticDao::dao('StaticDao') ) );
        $this->assertEquals( 'tests\DaoTest\StaticDao', get_class( \StaticDao::dao() ) );
        $this->assertEquals( 'you called me up', \StaticDao::callMe() );
    }
}