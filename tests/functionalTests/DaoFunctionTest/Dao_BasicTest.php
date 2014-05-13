<?php
namespace tests\ConstructTest;

use Illuminate\Database\Capsule\Manager as Capsule;
use Users;
use WScore\functionalTests\UsersModel\UserGender;
use WScore\functionalTests\UsersModel\UsersDao;
use WScore\functionalTests\UsersModel\UserStatus;

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
            'password' => '',
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
    function load_finds_a_user_entity_and_converts_some_value_to_enum()
    {
        $user_data = $this->createUser();
        $pKey = $user_data->getKey();
        $found = $this->dao->q()->where( 'user_id', '=', $pKey )->load();
        $this->assertEquals( 1, count( $found ) );
        $user = $found[0];
        $this->assertTrue( is_object( $user ) );
        $this->assertEquals( 'WScore\functionalTests\UsersModel\UserEntity', get_class( $user ) );
        $this->assertEquals( $pKey, $user['user_id'] );
        $this->assertEquals( $user_data->name, $user['name'] );
        $this->assertEquals( 'WScore\functionalTests\UsersModel\UserStatus', get_class($user['status'] ) );
        $this->assertEquals( 'WScore\functionalTests\UsersModel\UserGender', get_class($user['gender'] ) );
        /** @var UserStatus $status */
        $status=$user['status'];
        /** @var UserGender $gender */
        $gender=$user['gender'];
        $this->assertTrue( $status->isActive() );
        $this->assertTrue( $gender->is( UserGender::FEMALE ) );
    }

    /**
     * @test
     */
    function inset_adds_a_new_data()
    {
        $data = $this->getUserData();
        $id   = $this->dao->insert( $data );
        $user = Users::find($id);
        $this->assertEquals( $data['name'], $user['name'] );
    }

    /**
     * @test
     */
    function update_modifies_user_data()
    {
        $data = $this->getUserData();
        $name = 'update tested';
        $id   = $this->dao->insert( $data );
        $this->dao->q()->where( 'user_id','=', $id )->update(['name'=>$name]);
        $user = Users::find($id);
        $this->assertEquals( $name, $user['name'] );
    }

    /**
     * @test
     */
    function create_returns_user_entity_object()
    {
        $data = $this->getUserData();
        $user = $this->dao->create( $data );
        $this->assertEquals( 'WScore\functionalTests\UsersModel\UserEntity', get_class( $user ) );
        $this->assertEquals( $user->name, $data['name'] );
        $this->assertEquals( 'WScore\functionalTests\UsersModel\UserStatus', get_class($user['status'] ) );
        $this->assertEquals( 'WScore\functionalTests\UsersModel\UserGender', get_class($user['gender'] ) );
    }

    /**
     * @test
     */
    function save_inserts_a_created_entity()
    {
        $data = $this->getUserData();
        $user = $this->dao->create( $data );
        $this->dao->save( $user );
        $this->assertTrue( isset( $user->user_id ) );
        
        // check
        $saved = Users::find( $user->user_id );
        $this->assertEquals( $user->name, $saved->name );
    }

    /**
     * @test
     */
    function save_updates_a_loaded_entity()
    {
        $created_user = $this->createUser();
        $user_id = $created_user->user_id;
        $user = $this->dao->load($user_id);
        $user->name = 'testing-load-and=save';
        $this->dao->save($user);

        // check
        $saved = Users::find( $user_id );
        $this->assertEquals( $user['name'], $saved['name'] );
    }
}