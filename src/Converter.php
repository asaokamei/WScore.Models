<?php
namespace WScore\DbGateway;

use ArrayObject;
use DateTime;

/**
 * Class Converter
 *
 * @package WScore\DbGateway
 */
class Converter
{
    /**
     * @var Dao
     */
    protected $dao;

    /**
     * specify the format to convert an object to a string.
     * @var array
     */
    protected $formats = array(
        'datetime' => 'Y-m-d H:i:s'
    );

    protected $entityClass = '\\ArrayObject';

    /**
     * specify how a value maybe converted to an object.
     *
     * usage:
     * [ name_of_column => converter ]
     * where converter is
     * - callable function (or closure object),
     * - method name in the model,
     * - class name to create as new.
     *
     * @var array
     */
    protected $converts = array();

    // +----------------------------------------------------------------------+
    //  setup the converter
    // +----------------------------------------------------------------------+
    /**
     * @param $dao
     */
    public function setDao( $dao )
    {
        $this->dao = $dao;
    }

    /**
     * @param $name
     * @param $format
     */
    public function setDateTime( $name, $format=null ) {
        if( !$name ) return;
        if( !$format ) $format = $this->formats['datetime'];
        $this->formats[$name] = $format;
        $this->converts[$name] = 'toDateTime';
    }

    // +----------------------------------------------------------------------+
    //  some converters
    // +----------------------------------------------------------------------+

    /**
     * @param $date
     * @return DateTime
     */
    protected function toDateTime( $date )
    {
        return new DateTime($date);
    }

    /**
     * returns the list of columns. 
     * 
     * @param array|object $data
     * @return array
     */
    protected function listColumns( $data )
    {
        if( is_array( $data ) || !$this->dao ) {
            return array_keys($data);
        }
        return $this->dao->getColumns( $data );
    }
    // +----------------------------------------------------------------------+
    //  convert from array to entity object.
    // +----------------------------------------------------------------------+
    /**
     * @param array $data
     * @return ArrayObject
     */
    public function toEntity( $data )
    {
        $list = $this->listColumns( $data );
        $entity = $this->getNewEntity();
        foreach( $list as $name ) {
            if( is_array( $entity ) ) {
                $entity = $this->set( $entity, $name, $data[$name] );
            } else {
                $this->set( $entity, $name, $data[$name] );
            }
        }
        return $entity;
    }

    /**
     * @param ArrayObject|array $entity
     * @param string $name
     * @param mixed $value
     * @return ArrayObject
     */
    public function set( & $entity, $name, $value )
    {
        $object = $this->convertToObject( $name, $value );
        $this->setRawAttribute( $entity, $name, $object );
        return $entity;
    }

    /**
     * @return array|ArrayObject
     */
    protected function getNewEntity()
    {
        return new $this->entityClass;
    }

    /**
     * @param $name
     * @param $value
     * @return mixed
     */
    protected function convertToObject( $name, $value )
    {
        if( is_object( $value ) ) return $value;
        $method = 'set'.$this->up($name);
        if( method_exists( $this, $method ) ) {
            // primitive setter defined in Converter class. 
            // for entity objects, define such setter in its entity class. 
            return $this->$method( $value );
        }
        if( array_key_exists( $name, $this->converts ) ) {
            $converter = $this->converts[$name];
            if( is_callable( $converter ) ) {
                return $converter($value);
            }
            if( is_string( $converter ) && method_exists( $this, $converter ) ) {
                return $this->$converter($value);
            }
            if( is_string( $converter ) && class_exists( $converter ) ) {
                return new $converter($value);
            }
        }
        return $value;
    }

    /**
     * @param array|object $data
     * @param string $name
     * @param mixed $value
     */
    protected function setRawAttribute( &$data, $name, $value )
    {
        if( is_array( $data ) ) {
            $data[ $name ] = $value;
            return;
        }
        $method = 'set'.$this->up($name);
        if( is_object( $data ) && method_exists( $data, $method ) ) {
            $data->$method( $$value );
            return;
        }
        if( $data instanceof \ArrayAccess ) {
            $data[$name] = $value;
            return;
        }
    }

    // +----------------------------------------------------------------------+
    //  convert from entity object to an array.
    // +----------------------------------------------------------------------+
    /**
     * @param ArrayObject $data
     * @return array
     */
    public function toArray( $data )
    {
        $list = $this->listColumns( $data );
        $array = array();
        foreach( $list as $name ) {
            $value = $this->get( $data, $name );
            $array[$name] = $this->convertToString( $name, $value );
        }
        return $array;
    }

    /**
     * @param array|object $data
     * @param string $name
     * @return mixed
     */
    public function get( $data, $name )
    {
        if( is_array( $data ) ) {
            return isset($data[ $name ]) ? $data[ $name ]: null ;
        }
        $method = 'get'.$this->up($name);
        if( is_object( $data ) && method_exists( $data, $method ) ) {
            return $data->$method();
        }
        if( $data instanceof \ArrayAccess && isset( $data[$name]) ) {
            return $data[$name];
        }
        return null;
    }

    /**
     * @param $name
     * @param $value
     * @return array|mixed|string
     */
    protected function convertToString( $name, $value )
    {
        if( is_object( $value ) ) {
            if( isset( $this->formats[$name] ) && method_exists( $value, 'format' ) ) {
                $format = $this->formats[$name];
                $value = $value->format($format);
            }
            elseif( $value instanceof DateTime ) {
                $format = isset( $this->formats['datetime'] ) ? $this->formats['datetime']: '';
                $value = $value->format($format);
            }
            elseif( method_exists( $value, '__toString' ) ) {
                $value = $value->__toString();
            }
            $this->setRawAttribute($data, $name, $value );
        }
        return $value;
    }

    /**
     * @param string $name
     * @return string
     */
    protected function Up( $name )
    {
        $list = explode( '_', $name );
        $up = '';
        foreach( $list as $w ) {
            $up .= ucfirst( $w );
        }
        return $up;
    }
    // +----------------------------------------------------------------------+

}