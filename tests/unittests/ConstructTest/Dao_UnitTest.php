<?php
namespace tests\ConstructTest;

use Mockery as m;

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
            $db
        );
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
    function create_date_sets_format()
    {
        $formats = $this->dao->_getAny('formats');
        $this->assertEquals( 3, count( $formats ) );
        $this->assertEquals('Y-m-d H:i:s', $formats['created_at'] );
        $this->assertEquals('Y-m-d H:i:s', $formats['updated_at'] );
        $this->assertEquals('Y-m-d', $formats['creation_date'] );
    }

    /**
     * @test
     */
    function create_date_sets_convert()
    {
        $converts = $this->dao->_getAny('converts');
        $this->assertEquals( 3, count( $converts ) );
        $this->assertEquals('getCurrentTime', $converts['created_at'] );
        $this->assertEquals('getCurrentTime', $converts['updated_at'] );
        $this->assertEquals('getCurrentTime', $converts['creation_date'] );
    }

    /**
     * @test
     */
    function _updateTimeStamps_sets_timestamp_object()
    {
        $stamps = array( 'test_stamp');
        $data   = $orig = array( 'test' => 'testing' );
        $this->dao->call_updateTimeStamps( $data, $stamps );
        $this->assertEquals( $orig['test'], $data['test'] );
        $this->assertTrue( isset( $data['test_stamp'] ) );
        $this->assertTrue( is_object( $data['test_stamp'] ) );
        $this->assertTrue( method_exists( $data['test_stamp'], 'format' ) );
        $this->assertEquals( date('Y'), $data['test_stamp']->format('Y') );
    }
}