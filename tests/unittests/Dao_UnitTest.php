<?php
namespace Tests;

use Mockery as m;
use WScore\DbGateway\Dao;

require_once( dirname(__DIR__).'/autoload.php' );

class Dao_UnitTest extends \PHPUnit_Framework_TestCase
{
    var $dao;
    
    function setup()
    {
        class_exists( 'WScore\DbGateway\Dao' );
    }

    /**
     * @test
     */
    function construct_calls_db_table_once()
    {
        $db = m::mock( 'Illuminate\Database\Capsule\Manager' );
        $db->shouldReceive('table')->once();
        $dao = new Dao(
            $db
        );
        $this->assertEquals( 'WScore\DbGateway\Dao', get_class( $dao ));
    }
}