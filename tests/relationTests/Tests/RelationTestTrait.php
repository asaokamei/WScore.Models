<?php
namespace tests\relationTests\Tests;

use tests\relationTests\BlogModels\AuthorAR;
use tests\relationTests\BlogModels\AuthorGender;
use tests\relationTests\BlogModels\AuthorStatus;
use tests\relationTests\BlogModels\BlogAR;

trait RelationTestTrait
{
    /**
     * @return int
     */
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

    /**
     * @return array
     */
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

    /**
     * @return AuthorAR
     */
    function createBlog()
    {
        $blog = BlogAR::create( $this->getBlogData() );
        return $blog;
    }


}