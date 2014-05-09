<?php
namespace tests\ConstructTest;

use Mockery as m;
use WScore\DbGateway\Converter;

require_once( dirname( dirname( __DIR__ ) ) . '/autoload.php' );
require_once( __DIR__ . '/TestDao.php' );

class Dao_UnitTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var TestDao
     */
    var $dao;
    
    function setup()
    {
        class_exists( 'WScore\DbGateway\TestDao' );
        $db = m::mock( 'Illuminate\Database\Capsule\Manager' );
        $db->shouldReceive('table')->once();
        $this->dao = new TestDao(
            $db,
            new Converter()
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
        $this->assertEquals( 'tests\ConstructTest\TestDao', get_class( $this->dao ));
    }

    /**
     * @test
     */
    function auto_table_sets_table_name()
    {
        $this->assertEquals( 'TestDao', $this->dao->_getAny('table') );
    }

    /**
     * @test
     */
    function auto_table_sets_primaryKey()
    {
        $this->assertEquals( 'TestDao_id', $this->dao->_getAny('primaryKey') );
    }

    /**
     * @test
     */
    function updateTimeStamps_and_toString_convert_date_insert()
    {
        $data   = array( 'test' => 'testing' );
        $this->dao->callUpdateTimeStamps( $data, true );
        $value  = $this->dao->callToString( $data );
        $this->assertEquals( 4, count( $value ) );
        $this->assertEquals( $data['test'], $value['test'] );
        $this->assertTrue( isset( $value['created_at']));
        $this->assertTrue( isset( $value['creation_date']));
        $this->assertTrue( isset( $value['updated_at']));
        // checking datetime contents
        $date1 = new \DateTime($value['created_at']);
        /** @noinspection PhpUndefinedMethodInspection */
        $this->assertEquals( $date1, $data['created_at'] );
    }

    /**
     * @test
     */
    function updateTimeStamps_and_toString_convert_date_update()
    {
        $data   = array( 'test' => 'testing' );
        $this->dao->callUpdateTimeStamps( $data );
        $value  = $this->dao->callToString( $data );
        $this->assertEquals( 2, count( $value ) );
        $this->assertEquals( $data['test'], $value['test'] );
        $this->assertTrue( isset( $value['updated_at']));
        // checking datetime contents
        $date1 = new \DateTime($value['updated_at']);
        /** @noinspection PhpUndefinedMethodInspection */
        $this->assertEquals( $date1->format('Y-m-d H:i:s'), $value['updated_at'] );
    }

    /**
     * @test
     */
    function toObject_converts_createdAt_to_datetime_object()
    {
        $now = '2014-05-06 12:30:00';
        $data = array(
            $this->dao->_getAny( 'created_at' ) => $now
        );
        $entity = $this->dao->callToObject( $data );
        $this->assertEquals( 'DateTime', get_class( $entity['created_at'] ) );
        /** @noinspection PhpUndefinedMethodInspection */
        $this->assertEquals( $now, $entity['created_at']->format('Y-m-d H:i:s') );
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