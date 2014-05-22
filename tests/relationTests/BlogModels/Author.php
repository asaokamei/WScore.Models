<?php
namespace tests\relationTests\BlogModels;

/**
 * Class Author
 * @package tests\relationTests\BlogModels
 *
 */
class Author implements \ArrayAccess
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
     * @param array $data
     */
    public function __construct( $data=[] )
    {
        $this->assign($data);
    }

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
     * @param $data
     */
    protected function assign( $data )
    {
        foreach( $data as $offset => $value ) {
            $this->offsetSet( $offset, $value );
        }
    }
    /**
     * @param $name
     * @return null
     */
    public function __get( $name )
    {
        return isset( $this->$name )? $this->$name: null;
    }

    /**
     * Whether a offset exists
     * @param mixed $offset
     * @return boolean
     */
    public function offsetExists( $offset )
    {
        return isset( $this->$offset );
    }

    /**
     * Offset to retrieve
     * @param mixed $offset
     * @return mixed
     */
    public function offsetGet( $offset )
    {
        return isset( $this->$name )? $this->$name: null;
    }

    /**
     * sets value to offset, only if the offset is not in the property list.
     *
     * @param mixed $offset
     * @param mixed $value
     * @throws \InvalidArgumentException
     * @return void
     */
    public function offsetSet( $offset, $value )
    {
        if( isset( $this->$offset ) ) {
            throw new \InvalidArgumentException( "Cannot modify property in Entity object" );
        }
        $this->$offset = $value;
    }

    /**
     * @param mixed $offset
     * @throws \InvalidArgumentException
     * @return void
     */
    public function offsetUnset( $offset )
    {
        throw new \InvalidArgumentException( "Cannot unset property in Entity object" );
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

    /**
     * @return mixed
     */
    public function getBirthDate()
    {
        return $this->birth_date;
    }

    /**
     * @param mixed $birth_date
     */
    public function setBirthDate( $birth_date )
    {
        $this->birth_date = $birth_date;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     */
    public function setEmail( $email )
    {
        $this->email = $email;
    }
}