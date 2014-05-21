<?php
namespace WScore\Models\Dao;

use ArrayObject;
use DateTime;
use WScore\Models\DaoArray;
use WScore\Models\Entity\Magic;

/**
 * Class Converter
 *
 * @package WScore\Models
 */
class Converter
{
    /**
     * specify the format to convert an object to a string.
     * @var array
     */
    protected $formats = array(
        'datetime' => 'Y-m-d H:i:s'
    );

    protected $entityClass = '\\ArrayObject';

    /**
     * list of columns in the entity object.
     *
     * @var array
     */
    protected $columns = null;

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
     * @param $class
     */
    public function setEntityClass( $class ) {
        if( $class ) {
            $this->entityClass = $class;
        }
    }

    /**
     * @param array $columns
     */
    public function setColumns( $columns ) {
        $this->columns = $columns;
    }

    // +----------------------------------------------------------------------+
    //  filter hooks
    // +----------------------------------------------------------------------+
    /**
     * @param array $data
     * @return ArrayObject|object
     */
    public function onSavingFilter( $data )
    {
        return $this->toEntity( $data );
    }

    /**
     * @param $list
     * @return array
     */
    public function onLoaded( $list )
    {
        if( isset( $list[0] ) && is_array( $list[0] ) ) {
            foreach( $list as $k => $data ) {
                $list[$k] = $this->toArray($data);
            }
            return $list;
        }
        return $this->toArray( $list );
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
     * @throws \RuntimeException
     * @return array
     */
    protected function listColumns( $data )
    {
        if( is_array( $data ) ) {
            return array_keys($data);
        }
        if( $this->columns ) {
            return $this->columns;
        }
        throw new \RuntimeException( 'cannot find columns' );
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
            $this->set( $entity, $name, $data[$name] );
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
        Magic::set( $entity, $name, $object );
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
        $method = 'mute'.Magic::upCamelCase($name).'Attribute';
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
            $value = Magic::get( $data, $name );
            $array[$name] = $this->convertToString( $name, $value );
        }
        return $array;
    }

    /**
     * @param $name
     * @param $value
     * @return array|mixed|string
     */
    protected function convertToString( $name, $value )
    {
        $format = null;
        if( isset( $this->formats[$name] ) ) {
            $format = $this->formats[$name];
        }
        return Magic::evaluate( $value, $format );
    }

    // +----------------------------------------------------------------------+
}