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
}