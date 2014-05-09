<?php
namespace tests\ConstructTest;

use Illuminate\Database\Capsule\Manager as Capsule;
use Users;
use WScore\functionalTests\UsersModel\UsersDao;

require_once( dirname( dirname( __DIR__ ) ) . '/autoload.php' );
require_once( dirname( __DIR__ ) . '/config.php' );

class Dao_BasicTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var UsersDao
     */
    var $dao;

    /**
     * @var Capsule
     */
    var $capsule;
    
    function setup()
    {
        $this->capsule = \ConfigDB::getCapsule();
        $this->dao = UsersDao::getInstance( $this->capsule );
        \ConfigDB::setupTable();
    }
    
    function test0()
    {
        $this->assertEquals( 'Illuminate\Database\Capsule\Manager', get_class($this->capsule) );
        $this->assertEquals( 'Illuminate\Database\Eloquent\Builder', get_class(\Users::query()) );
        $this->assertEquals( 'WScore\functionalTests\UsersModel\UsersDao', get_class($this->dao) );
    }
    
    function test1()
    {
        Users::create([
            'status' => 1,
            'gender' => 'F',
            'name'   => 'test1 name',
            'birth_date' => '1989-01-23',
            'email'  => 'models@example.com',
        ]);
    }
}