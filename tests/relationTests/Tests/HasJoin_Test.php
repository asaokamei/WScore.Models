<?php
namespace tests\relationTests\Tests;

use Illuminate\Database\Capsule\Manager as Capsule;
use tests\relationTests\BlogModels\Author_RoleDao;
use tests\relationTests\BlogModels\AuthorAR;
use tests\relationTests\BlogModels\AuthorDao;
use tests\relationTests\BlogModels\RoleAR;
use tests\relationTests\BlogModels\RoleDao;

require_once( dirname( dirname( __DIR__ ) ) . '/autoload.php' );
require_once( dirname( __DIR__ ) . '/ConfigBlog.php' );

class HasJoin_Test extends \PHPUnit_Framework_TestCase
{
    use RelationTestTrait;

    /**
     * @var AuthorDao
     */
    var $daoAuth;

    /**
     * @var RoleDao
     */
    var $daoRole;

    var $daoAuthRole;
    
    /**
     * @var Capsule
     */
    var $capsule;

    function setup()
    {
        $this->capsule = \ConfigBlog::getCapsule();
        $this->daoAuth = AuthorDao::getInstance( $this->capsule );
        $this->daoRole = RoleDao::getInstance( $this->capsule );
        $this->daoAuthRole = Author_RoleDao::getInstance( $this->capsule );
        \ConfigBlog::setupTables();
    }

    /**
     * @param int $num_role
     * @return array
     */
    function addDbAuthorRole( $num_role=3 )
    {
        $data   = array();
        $author = AuthorAR::create( $this->getUserData() );
        $data['author'] = $author;
        for( $i=0; $i<$num_role; $i++ ) {
            $role = RoleAR::create( $this->getRoleData() );
            $data['role'][] = $role;
        }
        return $data;
    }

    function test0()
    {
        $data = $this->addDbAuthorRole();
        $this->assertEquals( 3, count( $data['role'] ) );

        $this->assertEquals( 'Illuminate\Database\Capsule\Manager', get_class($this->capsule) );
        $this->assertEquals( 'Illuminate\Database\Eloquent\Builder', get_class(AuthorAR::query()) );
        $this->assertEquals( 'tests\relationTests\BlogModels\RoleDao', get_class($this->daoRole) );
        $this->assertEquals( 'tests\relationTests\BlogModels\AuthorAR', get_class( $this->createUser() ) );
    }

    /**
     * @test
     */
    function HasJoin_relates_author2role_and_loads_role2author()
    {
        // relate author to role.
        $data = $this->addDbAuthorRole();
        $auth_id = $data['author']['author_id'];
        $author  = $this->daoAuth->load($auth_id);
        $roles   = [];
        $roles[] = $this->daoRole->load($data['role'][0]['role_id']);
        $roles[] = $this->daoRole->load($data['role'][2]['role_id']);
        
        $this->daoAuth->relate( $author, 'roles' )->relate( $roles );
    }
}