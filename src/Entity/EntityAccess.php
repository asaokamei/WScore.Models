<?php
namespace WScore\Models\Entity;

/**
 * Class EntityAccess
 * @package WScore\Models\Entity
 *
 * provide basic access method to entity (as an example).
 *
 * general rule:
 * if property is set, access through getter and setter.
 * if not set, access through magic methods or array-access.
 * cannot overwrite an existing value; can set only once.
 */
class EntityAccess implements \ArrayAccess
{
    /**
     * @param array $data
     */
    public function __construct( $data=[] )
    {
        $this->assign($data);
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
     * @param $offset
     * @return null
     */
    public function __get( $offset )
    {
        return $this->offsetGet( $offset );
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
        return isset( $this->$offset )? $this->$offset: null;
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

}