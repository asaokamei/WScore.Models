<?php
namespace tests\ConstructTest;

use Illuminate\Database\Capsule\Manager as Capsule;
use Users;
use WScore\functionalTests\UsersModel\UserGender;
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

    /**
     * @return array
     */
    function getUserData()
    {
        return [
            'status' => 1,
            'gender' => 'F',
            'name'   => 'name:'.mt_rand(1000,9999),
            'birth_date' => '1989-01-23',
            'email'  => 'm'.mt_rand(1000,9999).'@example.com',
        ];
    }
    
    /**
     * @return Users
     */
    function createUser()
    {
        $user = Users::create( $this->getUserData() );
        return $user;
    }
    
    function test0()
    {
        $this->assertEquals( 'Illuminate\Database\Capsule\Manager', get_class($this->capsule) );
        $this->assertEquals( 'Illuminate\Database\Eloquent\Builder', get_class(\Users::query()) );
        $this->assertEquals( 'WScore\functionalTests\UsersModel\UsersDao', get_class($this->dao) );
        $this->assertEquals( 'Users', get_class( $this->createUser() ) );
    }

    /**
     * @test
     */
    function UserDao_finds_a_user_data()
    {
        $user_data = $this->createUser();
        $pKey = $user_data->getKey();
        $found = $this->dao->where( 'user_id', '=', $pKey )->select();
        $this->assertEquals( 1, count( $found ) );
        $user = $found[0];
        $this->assertTrue( is_object( $user ) );
        $this->assertEquals( 'WScore\functionalTests\UsersModel\UserEntity', get_class( $user ) );
        $this->assertEquals( $pKey, $user['user_id'] );
        $this->assertEquals( $user_data->name, $user['name'] );
        $this->assertEquals( 'WScore\functionalTests\UsersModel\UserStatus', get_class($user['status'] ) );
        $this->assertEquals( 'WScore\functionalTests\UsersModel\UserGender', get_class($user['gender'] ) );
        $this->assertTrue( $user['status']->isActive() );
        $this->assertTrue( $user['gender']->is( UserGender::FEMALE ) );
    }

    /**
     * @test
     */
    function UserDao_find_returns_user_data()
    {
        $user = $this->createUser();
        $pKey = $user->getKey();
        $daoUser = $this->dao->where( 'user_id', '=', $pKey )->first();
        $this->assertTrue( is_array( $daoUser ) );
        $this->assertEquals( $pKey, $daoUser['user_id'] );
        $this->assertEquals( $user->name, $daoUser['name'] );
    }

    /**
     * @test
     */
    function UserDao_inset_add_new_data()
    {
        $data = $this->getUserData();
        $id   = $this->dao->insert( $data );
        $user = Users::find($id);
        $this->assertEquals( $data['name'], $user['name'] );
    }

    /**
     * @test
     */
    function UserDao_update_modifies_user_data()
    {
        $data = $this->getUserData();
        $name = 'update tested';
        $id   = $this->dao->insert( $data );
        $this->dao->where( 'user_id','=', $id )->update(['name'=>$name]);
        $user = Users::find($id);
        $this->assertEquals( $name, $user['name'] );
    }
}