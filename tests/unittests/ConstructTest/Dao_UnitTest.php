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
    function create_date_sets_format_and_convert()
    {
        $formats = $this->dao->_getAny('formats');
        $this->assertEquals( 3, count( $formats ) );
        $this->assertEquals('Y-m-d H:i:s', $formats['created_at'] );
        $this->assertEquals('Y-m-d H:i:s', $formats['updated_at'] );
        $this->assertEquals('Y-m-d', $formats['creation_date'] );
    }
}