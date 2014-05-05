<?php
namespace tests\ConstructTest;

use Mockery as m;

require_once( dirname( dirname( __DIR__ ) ) . '/autoload.php' );
require_once( __DIR__ . '/TestDao.php' );

class Dao_UnitTest extends \PHPUnit_Framework_TestCase
{
    var $dao;
    
    function setup()
    {
        class_exists( 'WScore\DbGateway\TestDao' );
    }

    /**
     * @test
     */
    function construct_calls_db_table_once()
    {
        $db = m::mock( 'Illuminate\Database\Capsule\Manager' );
        $db->shouldReceive('table')->once();
        $dao = new TestDao(
            $db
        );
        $this->assertEquals( 'tests\ConstructTest\TestDao', get_class( $dao ));
    }

    /**
     * @test
     */
    function auto_table_sets_table_name()
    {
        $db = m::mock( 'Illuminate\Database\Capsule\Manager' );
        $db->shouldReceive('table')->once();
        $dao = new TestDao(
            $db
        );
        $this->assertEquals( 'TestDao', $dao->_getAny('table') );
    }
}