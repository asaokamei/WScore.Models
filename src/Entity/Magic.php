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
        if( static::isCollection($data) ) {
            $values = array();
            foreach( $data as $key => $datum ) {
                $values[$key] = static::get( $datum, $name );
            }
            return $values;
        }
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
     * @param array|object  $data
     * @param string        $name
     * @param string|object $value
     * @return array|\ArrayAccess|object
     */
    static public function set( &$data, $name, $value )
    {
        if( !$name ) return $data;
        if( static::isCollection($data) ) {
            foreach( $data as $key => $datum ) {
                $data[$key] = static::set( $datum, $name, $value );
            }
            return $data;
        }
        if( is_array( $data ) ) {
            $data[ $name ] = $value;
            return $data;
        }
        $method = 'set'.static::upCamelCase($name);
        if( is_object( $data ) && method_exists( $data, $method ) ) {
            $data->$method( $value );
            return $data;
        }
        if( $data instanceof \ArrayAccess ) {
            $data[$name] = $value;
            return $data;
        }
        return $data;
    }

    /**
     * @param array|object $data
     * @return bool
     */
    public static function isCollection( $data )
    {
        if( is_object($data) && $data instanceof \ArrayAccess && isset( $data[0] ) ) {
            return true;
        }
        if( is_array($data) && isset( $data[0] ) ) {
            return true;
        }
        return false;
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