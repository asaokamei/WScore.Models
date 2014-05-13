<?php
namespace tests\ConstructTest;

use Illuminate\Database\Capsule\Manager;
use Illuminate\Database\Query\Builder;
use Mockery as m;
use Mockery\MockInterface;
use WScore\DbGateway\Converter;

require_once( dirname( dirname( __DIR__ ) ) . '/autoload.php' );
require_once( __DIR__ . '/TestDaoArray.php' );

class DaoArray_UnitTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var TestDao
     */
    var $dao;

    /**
     * @var Manager|MockInterface
     */
    var $db;

    /**
     * @var Builder|MockInterface
     */
    var $query;
    
    function setup()
    {
        class_exists( 'WScore\DbGateway\TestDaoArray' );
        $this->dao = $this->setDao();
    }

    /**
     * @return TestDao
     */
    public function setDao()
    {
        // construct TestDao using mocked db.
        $db = m::mock( 'Illuminate\Database\Capsule\Manager' );
        $db->shouldReceive('table')->once();
        $this->dao = new TestDaoArray(
            $db,
            new Converter()
        );
        // replaced the first mocked db with the new one. 
        m::close();
        $this->db = m::mock( 'Illuminate\Database\Capsule\Manager' );
        $this->dao->_setAny( 'db', $this->db );
        // and set the mocked query as well.  
        $this->query = m::mock('Illuminate\Database\Query\Builder');
        $this->dao->_setAny( 'query', $this->query );
        return $this->dao;
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

    /**
     * @test
     */
    function query_calls_table_and_refreshes_lastQuery()
    {
        $this->db->shouldReceive('table')->once()->andReturn('testQuery');
        $returned = $this->dao->query();
        $this->assertEquals( 'testQuery', $returned );
    }

    /**
     * @test
     */
    function insert_calls_query_insert_and_other_stuff()
    {
        $data = ['test'=>'tested'];
        $dao = $this->dao;
        $this->db->shouldReceive('table')->once();
        $this->query->shouldReceive('insertGetId')->andReturn('testID');
        $id = $dao->insert( $data );
        $this->assertEquals( 'testID', $id );
        $this->assertEquals( ['test'=>'tested'], $data );
        $data2 = $dao->_getAny('lastValue');
        $this->assertNotEquals( ['test'=>'tested'], $data2 );
        $this->assertTrue( isset( $data2['updated_at'] ) );
        $this->assertTrue( isset( $data2['created_at'] ) );
        $this->assertTrue( isset( $data2['creation_date'] ) );
        $this->assertEquals( 'testID', $data2['TestDaoArray_id'] );
    }

    /**
     * @test
     */
    function insertSerial_false_calls_just_insert()
    {
        $data = ['test'=>'tested'];
        $dao = $this->dao;
        $this->db->shouldReceive('table')->once();
        $this->query->shouldReceive('insert')->andReturn(true);
        $dao->_setAny( 'insertSerial', false );
        $success = $dao->insert( $data );
        $this->assertEquals( true, $success );
        $this->assertEquals( ['test'=>'tested'], $data );
        $data2 = $dao->_getAny('lastValue');
        $this->assertNotEquals( ['test'=>'tested'], $data2 );
        $this->assertTrue( isset( $data2['updated_at'] ) );
        $this->assertTrue( isset( $data2['created_at'] ) );
        $this->assertTrue( isset( $data2['creation_date'] ) );
        $this->assertFalse( isset( $data2['TestDaoArray_id'] ) );
    }

    /**
     * @test
     */
    function delete_calls_query_delete()
    {
        $dao = $this->dao;
        $this->db->shouldReceive('table')->once();
        $this->query->shouldReceive('delete')->with('testID');
        $dao->delete('testID');
        $this->assertTrue(true);
    }

    /**
     * @test
     */
    function update_calls_query_update()
    {
        $data = ['test'=>'tested'];
        $dao = $this->dao;
        $this->db->shouldReceive('table')->once();
        $this->query->shouldReceive('update')->andReturn(true);
        $ok = $dao->update( $data );
        $this->assertEquals( true, $ok );
        $this->assertEquals( ['test'=>'tested'], $data );
        $data2 = $dao->_getAny('lastValue');
        $this->assertNotEquals( ['test'=>'tested'], $data2 );
        $this->assertTrue( isset( $data2['updated_at'] ) );
    }

    /**
     * @test
     */
    function update_with_id_calls_query_update_and_where()
    {
        $data = ['test'=>'tested'];
        $dao = $this->dao;
        $this->db->shouldReceive('table')->once();
        $this->query->shouldReceive('where')->once()->with( 'TestDaoArray_id', '=', 'testID' );
        $this->query->shouldReceive('update')->once()->andReturn(true);
        $ok = $dao->update( 'testID', $data );
        $this->assertEquals( true, $ok );
        $this->assertEquals( ['test'=>'tested'], $data );
        $data2 = $dao->_getAny('lastValue');
        $this->assertNotEquals( ['test'=>'tested'], $data2 );
        $this->assertTrue( isset( $data2['updated_at'] ) );
    }

    /**
     * @test
     */
    function select_calls_query_select()
    {
        $dao = $this->dao;
        $this->db->shouldReceive('table')->once();
        $this->query->shouldReceive('select->get')->andReturn(array(['test'=>'tested']));
        $data = $dao->select();
        $this->assertEquals( 1, count( $data ));
        $this->assertEquals( 'tested', $data[0]['test'] );
    }

}