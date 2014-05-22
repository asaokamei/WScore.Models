<?php
namespace tests\relationTests\Test;

use tests\relationTests\BlogModels\Author;
use tests\relationTests\BlogModels\AuthorAR;
use tests\relationTests\BlogModels\AuthorDao;
use Illuminate\Database\Capsule\Manager as Capsule;
use tests\relationTests\BlogModels\AuthorGender;
use tests\relationTests\BlogModels\AuthorStatus;
use tests\relationTests\BlogModels\BlogDao;
use WScore\Models\Entity\Magic;

require_once( dirname( dirname( __DIR__ ) ) . '/autoload.php' );
require_once( dirname( __DIR__ ) . '/ConfigBlog.php' );

class Author_Test extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AuthorDao
     */
    var $daoAuth;

    /**
     * @var BlogDao
     */
    var $daoBlog;

    /**
     * @var Capsule
     */
    var $capsule;

    function setup()
    {
        $this->capsule = \ConfigBlog::getCapsule();
        $this->daoAuth = AuthorDao::getInstance( $this->capsule );
        $this->daoBlog = BlogDao::getInstance( $this->capsule );
        \ConfigBlog::setupTables();
    }

    function getRand() {
        return mt_rand(1000,9999);
    }
    /**
     * @return array
     */
    function getUserData()
    {
        return [
            'status' => AuthorStatus::ACTIVE,
            'password' => '',
            'gender' => AuthorGender::FEMALE,
            'name'   => 'name:'.mt_rand(1000,9999),
            'birth_date' => '1989-01-23',
            'email'  => 'm'.mt_rand(1000,9999).'@example.com',
        ];
    }

    function getBlogData()
    {
        return [
            'status' => '1', 
            'author_id' => null, 
            'title' => 'blog-title:'.$this->getRand(), 
            'content' => 'blog-content:'.$this->getRand(),
        ];
    }
    /**
     * @return AuthorAR
     */
    function createUser()
    {
        $user = AuthorAR::create( $this->getUserData() );
        return $user;
    }

    function test0()
    {
        $this->assertEquals( 'Illuminate\Database\Capsule\Manager', get_class($this->capsule) );
        $this->assertEquals( 'Illuminate\Database\Eloquent\Builder', get_class(AuthorAR::query()) );
        $this->assertEquals( 'tests\relationTests\BlogModels\AuthorDao', get_class($this->daoAuth) );
        $this->assertEquals( 'tests\relationTests\BlogModels\AuthorAR', get_class( $this->createUser() ) );
    }

    /**
     * @test
     */
    function load_finds_a_author_entity_and_converts_some_value_to_enum()
    {
        $user_data = $this->createUser();
        $pKey = $user_data->getKey();
        $found = $this->daoAuth->load( $pKey );
        $this->assertFalse( Magic::isCollection( $found ) );
        /** @var Author $author */
        $author = $found;
        $this->assertTrue( is_object( $author ) );
        $this->assertEquals( 'tests\relationTests\BlogModels\Author', get_class( $author ) );
        $this->assertEquals( $pKey, $author->author_id );
        $this->assertEquals( $user_data->name, $author->name );
        $this->assertEquals( 'tests\relationTests\BlogModels\AuthorStatus', get_class($author->getStatus() ) );
        $this->assertEquals( 'tests\relationTests\BlogModels\AuthorGender', get_class($author->getGender() ) );
        /** @var AuthorStatus $status */
        $status=$author->getStatus();
        /** @var AuthorGender $gender */
        $gender=$author->getGender();
        $this->assertTrue( $status->isActive() );
        $this->assertTrue( $gender->is( AuthorGender::FEMALE ) );
    }

    /**
     * @test
     */
    function HasMany_user2blog_wo_authorId_not_link_()
    {
        $blog = $this->daoBlog->create( $this->getBlogData() );
        $user = $this->daoAuth->create( $this->getUserData() );
        $linked = $this->daoAuth->relate($user, 'blogs')->relate($blog );
        $this->assertEquals( null, $blog['author_id'] );
        $this->assertEquals( false, $linked );
        $this->assertEquals( false, $linked );
    }

    /**
     * @test
     */
    function HasMany_user2blog_sets_author_id_in_blog_entity()
    {
        $blog = $this->daoBlog->create( $this->getBlogData() );
        $user = $this->daoAuth->create( $this->getUserData() );
        $user['author_id'] = 'test-ID';
        $linked = $this->daoAuth->relate($user, 'blogs')->relate($blog );
        $this->assertEquals( 'test-ID', $blog['author_id'] );
        $this->assertEquals( true, $linked );
    }
}
