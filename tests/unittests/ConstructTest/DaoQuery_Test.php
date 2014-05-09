<?php
namespace tests\ConstructTest;

use Illuminate\Database\Capsule\Manager;
use Illuminate\Database\Query\Builder;
use Mockery as m;
use Mockery\MockInterface;
use WScore\DbGateway\Converter;

require_once( dirname( dirname( __DIR__ ) ) . '/autoload.php' );
require_once( __DIR__ . '/TestDao.php' );

class DaoQuery_UnitTest extends \PHPUnit_Framework_TestCase
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
        class_exists( 'WScore\DbGateway\TestDao' );
    }
    
    public function tearDown() {
        m::close();
    }

    /**
     * @return TestDao
     */
    public function setDao()
    {
        // construct TestDao using mocked db.
        $db = m::mock( 'Illuminate\Database\Capsule\Manager' );
        $db->shouldReceive('table')->once();
        $this->dao = new TestDao(
            $db,
            new Converter()
        );
        // replaced the first mocked db with the new one. 
        m::close();
        $this->db = m::mock( 'Illuminate\Database\Capsule\Manager' );
        $this->dao->_setAny( 'db', $this->db );
        // and set the mocked query as well.  
        $this->query = m::mock('Illuminate\Database\Query\Builder');
        $this->dao->_setAny( 'lastQuery', $this->query );
        return $this->dao;
    }
    
    function test0()
    {
        $this->setDao();
        $this->assertEquals( 'tests\ConstructTest\TestDao', get_class($this->dao) );
    }

    /**
     * @test
     */
    function query_calls_table_and_refreshes_lastQuery()
    {
        $dao = $this->setDao();
        $this->db->shouldReceive('table')->once()->andReturn('testQuery');
        $returned = $dao->query();
        $this->assertEquals( 'testQuery', $returned );
    }

    /**
     * @test
     */
    function insert_calls_query_insert_and_other_stuff()
    {
        $data = ['test'=>'tested'];
        $dao = $this->setDao();
        $this->db->shouldReceive('table')->once();
        $this->query->shouldReceive('insertGetId')->andReturn('testID');
        $id = $dao->insert( $data );
        $this->assertEquals( 'testID', $id );
        $this->assertEquals( ['test'=>'tested'], $data );
        $data2 = $dao->_getAny('data');
        $this->assertNotEquals( ['test'=>'tested'], $data2 );
        $this->assertTrue( isset( $data2['updated_at'] ) );
        $this->assertTrue( isset( $data2['created_at'] ) );
        $this->assertTrue( isset( $data2['creation_date'] ) );
        $this->assertEquals( 'testID', $data2['TestDao_id'] );
    }

    /**
     * @test
     */
    function delete_calls_query_insert_and_other_stuff()
    {
        $dao = $this->setDao();
        $this->db->shouldReceive('table')->once();
        $this->query->shouldReceive('delete')->with('testID');
        $dao->delete('testID');
        $this->assertTrue(true);
    }

    /**
     * @test
     */
    function insert_arrayObject_()
    {
        $data = new \ArrayObject( ['test'=>'tested'] );
        $dao = $this->setDao();
        $this->db->shouldReceive('table')->once();
        $this->query->shouldReceive('insertGetId')->andReturn('testID');
        $id = $dao->insert( $data );
        $this->assertEquals( 'testID', $id );
        $this->assertTrue( isset( $data['updated_at'] ) );
        $this->assertTrue( isset( $data['created_at'] ) );
        $this->assertTrue( isset( $data['creation_date'] ) );
        $this->assertTrue( is_object( $data['updated_at'] ) );
        $this->assertTrue( is_object( $data['created_at'] ) );
        $this->assertTrue( is_string( $data['creation_date'] ) );
        $this->assertEquals( 'testID', $data['TestDao_id'] );
    }

    /**
     * @test
     */
    function update_calls_query_insert_and_other_stuff()
    {
        $data = ['test'=>'tested'];
        $dao = $this->setDao();
        $this->db->shouldReceive('table')->once();
        $this->query->shouldReceive('update')->andReturn(true);
        $ok = $dao->update( $data );
        $this->assertEquals( true, $ok );
        $this->assertEquals( ['test'=>'tested'], $data );
        $data2 = $dao->_getAny('data');
        $this->assertNotEquals( ['test'=>'tested'], $data2 );
        $this->assertTrue( isset( $data2['updated_at'] ) );
    }

    /**
     * @test
     */
    function select_calls_query_select()
    {
        $dao = $this->setDao();
        $this->db->shouldReceive('table')->once();
        $this->query->shouldReceive('select->get')->andReturn(array(['test'=>'tested']));
        $data = $dao->select();
        $this->assertEquals( 1, count( $data ));
        $this->assertEquals( 'ArrayObject', get_class( $data[0] ));
        $this->assertEquals( 'tested', $data[0]['test'] );
    }

    /**
     * @test
     */
    function insert_converts_DateTime_object_to_string()
    {
        $data = new \ArrayObject( ['test'=> new \DateTime('2014-05-09 11:23:45')] );
        $dao = $this->setDao();
        $this->db->shouldReceive('table')->once();
        $this->query->shouldReceive('insertGetId')->andReturn('testID');
        $id = $dao->insert( $data );
        $this->assertEquals( 'testID', $id );
        $value = $this->dao->lastValue;
        $this->assertTrue( isset( $value['test'] ) );
        $this->assertTrue( isset( $value['updated_at'] ) );
        $this->assertTrue( isset( $value['created_at'] ) );
        $this->assertTrue( isset( $value['creation_date'] ) );
        $this->assertEquals( '2014-05-09 11:23:45', $value['test'] );
        $this->assertTrue( is_string( $value['updated_at'] ) );
        $this->assertTrue( is_string( $value['created_at'] ) );
        $this->assertTrue( is_string( $value['creation_date'] ) );
        $this->assertEquals( 'testID', $value['TestDao_id'] );
    }

    /**
     * @test
     */
    function scopeTest_add_column_when_insert()
    {
        $data = new \ArrayObject( ['test'=> new \DateTime('2014-05-09 11:23:45')] );
        $dao = $this->setDao();
        $this->db->shouldReceive('table')->once();
        $this->query->shouldReceive('insertGetId')->andReturn('testID');
        $this->query->shouldReceive('where')->with('scope-test');
        $id = $dao->scopeTest()->insert( $data );
        $this->assertEquals( 'testID', $id );
        $value = $this->dao->lastValue;
        $this->assertTrue( isset( $value['test'] ) );
        $this->assertEquals( '2014-05-09 11:23:45', $value['test'] );
        $this->assertEquals( 'testID', $value['TestDao_id'] );
    }

    /**
     * @test
     */
    function dao_method_chain_works()
    {
        $data = new \ArrayObject( ['test'=> new \DateTime('2014-05-09 11:23:45')] );
        $dao = $this->setDao();
        $this->db->shouldReceive('table')->once();
        $this->query->shouldReceive('insertGetId')->andReturn('testID');
        $this->query->shouldReceive('where')->with('chain-test');
        $id = $dao->where('chain-test')->insert( $data );
        $this->assertEquals( 'testID', $id );
        $value = $this->dao->lastValue;
        $this->assertTrue( isset( $value['test'] ) );
        $this->assertEquals( '2014-05-09 11:23:45', $value['test'] );
        $this->assertEquals( 'testID', $value['TestDao_id'] );
    }
}