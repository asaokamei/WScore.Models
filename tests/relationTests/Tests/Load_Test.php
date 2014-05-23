<?php
namespace tests\relationTests\Tests;

use Illuminate\Support\Facades\DB;
use tests\relationTests\BlogModels\Author;
use tests\relationTests\BlogModels\AuthorAR;
use tests\relationTests\BlogModels\AuthorDao;
use tests\relationTests\BlogModels\BlogAR;
use tests\relationTests\BlogModels\BlogDao;
use Illuminate\Database\Capsule\Manager as Capsule;
use WScore\Models\Entity\EntityAccess;

require_once( dirname( dirname( __DIR__ ) ) . '/autoload.php' );
require_once( dirname( __DIR__ ) . '/ConfigBlog.php' );

class Load_Test extends \PHPUnit_Framework_TestCase
{
    use RelationTestTrait;

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

    /**
     * @param int $num_blog
     * @return array
     */
    function addDbAuthorBlog( $num_blog=2 )
    {
        $data   = array();
        $author = AuthorAR::create( $this->getUserData() );
        $data['author'] = $author;
        for( $i=0; $i<$num_blog; $i++ ) {
            $blog = $this->getBlogData();
            $blog[ $author->getKeyName() ] = $author->getKey();
            $blog = BlogAR::create( $blog );
            $data['blog'][] = $blog;
        }
        return $data;
    }

    function test0()
    {
        $data = $this->addDbAuthorBlog();
        $this->assertEquals( 2, count( $data['blog'] ) );

        $this->assertEquals( 'Illuminate\Database\Capsule\Manager', get_class($this->capsule) );
        $this->assertEquals( 'Illuminate\Database\Eloquent\Builder', get_class(AuthorAR::query()) );
        $this->assertEquals( 'tests\relationTests\BlogModels\AuthorDao', get_class($this->daoAuth) );
        $this->assertEquals( 'tests\relationTests\BlogModels\AuthorAR', get_class( $this->createUser() ) );
    }

    /**
     * @test
     */
    function load_author2blog_reads_blog_data_from_database()
    {
        $data = $this->addDbAuthorBlog();
        $auth_id = $data['author']['author_id'];
        /** @var Author $author */
        $author = $this->daoAuth->load( $auth_id );
        $this->assertEquals( $data['author']['name'], $author->name );

        /** @var EntityAccess[] $blogs */
        $blogs = $this->daoAuth->relate( $author, 'blogs' )->load();
        $this->assertEquals( 2, count( $blogs ) );

        $this->assertEquals( $data['blog'][0]['title'], $blogs[0]->title );
        $this->assertEquals( $data['blog'][1]['title'], $blogs[1]->title );
    }

    /**
     * @test
     */
    function load_blog2author_reads_author_data_from_database()
    {
        $data = $this->addDbAuthorBlog();
        $blog_id = $data['blog'][1]->getKey();
        $blog = $this->daoBlog->load($blog_id);
        $this->assertEquals( $data['blog'][1]['title'], $blog['title'] );

        $author = $this->daoBlog->relate( $blog, 'author' )->load();
        $this->assertEquals( $blog['author_id'], $author['author_id'] );
        $this->assertEquals( $data['author']['name'], $author['name'] );
    }
}