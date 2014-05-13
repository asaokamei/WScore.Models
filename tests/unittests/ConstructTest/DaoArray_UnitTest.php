<?php
namespace tests\ConstructTest;

use Mockery as m;
use WScore\DbGateway\Converter;

require_once( dirname( dirname( __DIR__ ) ) . '/autoload.php' );
require_once( __DIR__ . '/TestDaoArray.php' );

class DaoArray_UnitTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var TestDao
     */
    var $dao;
    
    function setup()
    {
        class_exists( 'WScore\DbGateway\TestDaoArray' );
        $db = m::mock( 'Illuminate\Database\Capsule\Manager' );
        $db->shouldReceive('table')->once();
        $this->dao = new TestDaoArray(
            $db,
            null
        );
    }

    public function tearDown() {
        m::close();
    }

    /**
     * @test
     */
    function construct_calls_db_table_once()
    {
        $this->assertEquals( 'tests\ConstructTest\TestDaoArray', get_class( $this->dao ));
    }

    /**
     * @test
     */
    function auto_table_sets_table_name()
    {
        $this->assertEquals( 'TestDaoArray', $this->dao->_getAny('table') );
    }

    /**
     * @test
     */
    function auto_table_sets_primaryKey()
    {
        $this->assertEquals( 'TestDaoArray_id', $this->dao->_getAny('primaryKey') );
    }

    /**
     * @test
     */
    function updateTimeStamps_and_toString_convert_date_insert()
    {
        $data   = array( 'test' => 'testing' );
        $this->dao->callUpdateTimeStamps( $data, true );
        $this->assertEquals( 4, count( $data ) );
        $this->assertTrue( isset( $data['created_at']));
        $this->assertTrue( isset( $data['creation_date']));
        $this->assertTrue( isset( $data['updated_at']));
        // checking datetime contents
        $date1 = new \DateTime($data['created_at']);
        /** @noinspection PhpUndefinedMethodInspection */
        $this->assertEquals( $date1->format('Y-m-d H:i:s'), $data['created_at'] );
    }

    /**
     * @test
     */
    function updateTimeStamps_and_toString_convert_date_update()
    {
        $data   = array( 'test' => 'testing' );
        $this->dao->callUpdateTimeStamps( $data );
        $this->assertEquals( 2, count( $data ) );
        $this->assertTrue( isset( $data['updated_at']));
        // checking datetime contents
        $date1 = new \DateTime($data['updated_at']);
        /** @noinspection PhpUndefinedMethodInspection */
        $this->assertEquals( $date1->format('Y-m-d H:i:s'), $data['updated_at'] );
    }

    /**
     * @test
     */
    function testHooks_fires_test_event_and_invoke_onTest()
    {
        $this->assertEquals( null, $this->dao->lastValue );
        $this->dao->testHooks( 'tested' );
        $this->assertEquals( 'hook-tested:tested', $this->dao->lastValue );
    }

}