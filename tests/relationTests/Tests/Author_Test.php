<?php
namespace tests\relationTests\Test;

use tests\relationTests\BlogModels\Author;
use tests\relationTests\BlogModels\AuthorAR;
use tests\relationTests\BlogModels\AuthorDao;
use Illuminate\Database\Capsule\Manager as Capsule;
use tests\relationTests\BlogModels\AuthorGender;
use tests\relationTests\BlogModels\AuthorStatus;

require_once( dirname( dirname( __DIR__ ) ) . '/autoload.php' );
require_once( dirname( __DIR__ ) . '/ConfigBlog.php' );

class Author_Test extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AuthorDao
     */
    var $dao;

    /**
     * @var Capsule
     */
    var $capsule;

    function setup()
    {
        $this->capsule = \ConfigBlog::getCapsule();
        $this->dao = AuthorDao::getInstance( $this->capsule );
        \ConfigBlog::setupTables();
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

    /**
     * @return Author
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
        $this->assertEquals( 'tests\relationTests\BlogModels\AuthorDao', get_class($this->dao) );
        $this->assertEquals( 'tests\relationTests\BlogModels\AuthorAR', get_class( $this->createUser() ) );
    }

}
