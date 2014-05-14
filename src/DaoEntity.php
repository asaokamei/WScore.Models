<?php
namespace WScore\DbGateway;

use ArrayAccess;
use RuntimeException;

class DaoEntity extends DaoArray
{
    /**
     * @var Converter
     */
    protected $convert;

    /**
     * keep the last data to be inserted, updated, or selected.
     *
     * @var array|ArrayAccess|mixed
     */
    protected $entity;

    /**
     * list of loaded entity's object hash.
     *
     * @var array
     */
    protected $loadedEntity = array();

    /**
     * list of deleted entities object hash. 
     * 
     * @var array
     */
    protected $deletedEntity = array();

    // +----------------------------------------------------------------------+
    //  entity related methods
    // +----------------------------------------------------------------------+
    /**
     * @param Converter $converter
     */
    public function setConverter( $converter )
    {
        $this->convert = $converter;
        $this->convert->setDao( $this );
        $this->convert->setDateTime( $this->created_at,   $this->date_formats );
        $this->convert->setDateTime( $this->updated_at,   $this->date_formats );
    }

    /**
     * @param array $data
     * @return array|object
     */
    public function create( $data )
    {
        $this->hooks( 'creating', $data );
        $entity = $this->convert->toEntity( $data );
        $this->hooks( 'created', $entity );
        return $entity;
    }

    /**
     * @param $entity
     */
    public function save( $entity )
    {
        $this->entity = $entity;
        $data = $this->convert->toArray($entity);
        $this->hooks( 'saving', $data );
        if( $this->isRetrieved($entity) ) {
            $id = $this->get( $entity, $this->primaryKey );
            $this->update( $id, $data );
        } else {
            $id = $this->insert( $data );
            $this->set( $entity, $this->primaryKey, $id );
            $this->hash( $entity );
        }
        $this->hooks( 'saved', $data );
    }

    /**
     * @param null|string $id
     * @return object[]
     */
    public function load($id=null)
    {
        $this->hooks( 'loading', $id );
        if( $id ) {
            $this->setId( $id );
        }
        $list = $this->select();
        foreach( $list as $key => $data ) {
            $list[$key] = $entity = $this->convert->toEntity( $data );
            $this->hash($entity);
        }
        $this->hooks( 'loaded', $list );
        if( $id ) {
            return $list[0];
        }
        return $list;
    }

    /**
     * @param $entity
     */
    public function remove( $entity )
    {
        $this->entity = $entity;
        $key = $this->get( $entity, $this->primaryKey );
        $this->hooks( 'removing', $key );
        $this->delete($key);
        $this->deletedEntity[] = spl_object_hash($entity);
        $this->hooks( 'removed', $key );
    }
    // +----------------------------------------------------------------------+
    //  converting entity to/from array data.
    // +----------------------------------------------------------------------+
    /**
     * @param array|ArrayAccess $data
     * @param $name
     * @param $value
     * @throws RuntimeException
     */
    public function set( & $data, $name, $value )
    {
        if( !$name ) return;
        $this->convert->set( $data, $name, $value );
        return;
    }

    /**
     * @param object $data
     * @param string $name
     * @return mixed
     */
    public function get( $data, $name )
    {
        if( !$name ) return null;
        return $this->convert->get( $data, $name );
    }

    /**
     * @param object $entity
     */
    protected function hash($entity) {
        $this->loadedEntity[] = spl_object_hash($entity);
    }

    /**
     * @param object $entity
     * @return bool
     */
    public function isRetrieved($entity) {
        return in_array( spl_object_hash($entity), $this->loadedEntity );
    }

    /**
     * @param object $entity
     * @return bool
     */
    public function isDeleted($entity) {
        return in_array( spl_object_hash($entity), $this->deletedEntity );
    }
    // +----------------------------------------------------------------------+
}