<?php
namespace WScore\Models\Entity;

class Magic
{
    static public $dateTimeFormat = 'Y-m-d H:i:s';
    
    /**
     * get a value from array or entity
     * 
     * @param array|object $data
     * @param string $name
     * @return mixed
     */
    static public function get( $data, $name )
    {
        if( !$name ) return null;
        if( is_array( $data ) ) {
            return isset( $data[$name] ) ? $data[ $name ]: null ;
        }
        $method = 'get'.static::upCamelCase($name);
        if( is_object( $data ) && method_exists( $data, $method ) ) {
            return $data->$method();
        }
        if( $data instanceof \ArrayAccess && isset( $data[$name]) ) {
            return $data[$name];
        }
        return null;
    }

    /**
     * @param array|object $data
     * @param string $name
     * @param mixed $value
     */
    static public function set( &$data, $name, $value )
    {
        if( !$name ) return;
        if( is_array( $data ) ) {
            $data[ $name ] = $value;
            return;
        }
        $method = 'set'.static::upCamelCase($name);
        if( is_object( $data ) && method_exists( $data, $method ) ) {
            $data->$method( $$value );
            return;
        }
        if( $data instanceof \ArrayAccess ) {
            $data[$name] = $value;
            return;
        }
    }

    /**
     * makes
     * 
     * @param string $name
     * @return string
     */
    static public function upCamelCase( $name )
    {
        $list = explode( '_', $name );
        $up = '';
        foreach( $list as $w ) {
            $up .= ucfirst( $w );
        }
        return $up;
    }

    /**
     * @param object      $value
     * @param string|null $format
     * @throws \RuntimeException
     * @return string|int|bool
     */
    static public function evaluate( $value, $format=null )
    {
        if( !is_object( $value ) ) {
            return $value;
        }
        if( $format && method_exists( $value, 'format' ) ) {
            return $value->format($format);
        }
        if( $value instanceof \DateTime ) {
            return $value->format( static::$dateTimeFormat );
        }
        elseif( method_exists( $value, '__toString' ) ) {
            return $value->__toString();
        }
        return $value;
        //throw new \RuntimeException( 'Cannot evaluate an object' );
    }
}