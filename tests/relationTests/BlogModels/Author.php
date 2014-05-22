<?php
namespace tests\relationTests\BlogModels;

use WScore\Models\Entity\EntityAccess;

/**
 * Class Author
 * @package tests\relationTests\BlogModels
 *
 */
class Author extends EntityAccess
{
    /**
     * @var AuthorStatus
     */
    protected $status;

    /**
     * @var AuthorGender
     */
    protected $gender;

    protected $password;

    /**
     * @return AuthorStatus
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param AuthorStatus $status
     */
    public function setStatus( $status )
    {
        $this->status = $status;
    }

    /**
     * @return AuthorGender
     */
    public function getGender()
    {
        return $this->gender;
    }

    /**
     * @param AuthorGender $gender
     */
    public function setGender( $gender )
    {
        $this->gender = $gender;
    }

    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param mixed $password
     */
    public function setPassword( $password )
    {
        $this->password = $password;
    }
}