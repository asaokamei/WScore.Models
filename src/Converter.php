<?php
namespace WScore\DbGateway;

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
    protected $formats = array();

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
    protected function setDateTime( $name, $format ) {
        if( !$name ) return;
        $this->formats[$name] = $format;
        $this->converts[$name] = 'toDateTime';
    }

    // +----------------------------------------------------------------------+
    //  some converters
    // +----------------------------------------------------------------------+

    /**
     * @param $date
     * @return \DateTime
     */
    protected function toDateTime( $date )
    {
        return new \DateTime($date);
    }

    // +----------------------------------------------------------------------+
    //  convert from array to entity object.
    // +----------------------------------------------------------------------+
    /**
     * @param array $data
     * @return array|object
     */
    public function toEntity( $data )
    {
        $list = $this->dao->getColumns( $data );
        $entity = $this->getNewEntity();
        foreach( $list as $name ) {
            $object = $this->convertToObject( $name, $data[$name] );
            $this->setRawAttribute( $entity, $name, $object );
        }
        return $entity;
    }

    /**
     * @return array
     */
    protected function getNewEntity()
    {
        return array();
    }

    /**
     * @param $name
     * @param $value
     * @return mixed
     */
    protected function convertToObject( $name, $value )
    {
        if( !is_string( $value ) ) return $value;
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
        $method = 'set'.ucwords($name);
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
     * @param $data
     * @return array|mixed|string
     */
    public function toArray( $data )
    {
        $list = $this->dao->getColumns( $data );
        $array = array();
        foreach( $list as $name ) {
            $value = $this->getRawAttribute( $data, $name );
            $array[$name] = $this->convertToString( $name, $value );
        }
        return $array;
    }

    /**
     * @param array|object $data
     * @param string $name
     * @return mixed
     */
    protected function getRawAttribute( $data, $name )
    {
        if( is_array( $data ) ) {
            return $data[ $name ];
        }
        $method = 'get'.ucwords($name);
        if( is_object( $data ) && method_exists( $data, $method ) ) {
            $data->$method();
        }
        if( $data instanceof \ArrayAccess ) {
            $data[$name];
        }
        return null;
    }

    /**
     * @param $data
     * @param $name
     * @return array|mixed|string
     */
    public function convertToString( $data, $name )
    {
        $value = $this->getRawAttribute( $data, $name );
        if( is_object( $value ) ) {
            if( $value instanceof \DateTime ) {
                $format = isset( $this->formats[$name] ) ? $this->formats[$name]: '';
                $value = $value->format($format);
            }
            elseif( method_exists( $value, 'format' ) ) {
                $format = isset( $this->formats[$name] ) ? $this->formats[$name]: '';
                $value = $value->format($format);
            }
            elseif( method_exists( $value, '__toString' ) ) {
                $value = $value->__toString();
            }
            $this->setRawAttribute($data, $name, $value );
        }
        return $value;
    }

    // +----------------------------------------------------------------------+

}