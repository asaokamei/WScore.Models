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
    }
}