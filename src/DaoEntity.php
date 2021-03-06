<?php
namespace WScore\Models;

use ArrayAccess;
use Illuminate\Database\Capsule\Manager;
use WScore\Models\Dao\Converter;
use WScore\Models\Dao\Relation;
use WScore\Models\Dao\Relation\RelationAbstract;
use WScore\Models\Dao\TimeStamp;
use WScore\Models\Entity\Magic;

class DaoEntity extends DaoArray
{
    /**
     * @var Converter
     */
    protected $convert;

    /**
     * @var Relation
     */
    protected $relation;

    /**
     * keep the last data to be inserted, updated, or selected.
     *
     * @var array|ArrayAccess|mixed
     */
    protected $entity;

    /**
     * entity class
     *
     * @var string
     */
    protected $entityClass = '\WScore\Models\Entity\EntityAccess';

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

    /**
     * @param Manager $db
     * @param TimeStamp|null $stamps
     * @param Converter|null $converter
     * @param Relation|null  $relation
     * @return DaoEntity
     */
    public static function getInstance( $db, $stamps=null, $converter=null, $relation=null )
    {
        /** @var DaoEntity $dao */
        $dao = new static( $db );
        if( !$stamps ) $stamps = new TimeStamp();
        if( !$converter ) $converter = new Converter();
        if( !$relation ) $relation = new Relation();
        $dao->setTimeStamps( $stamps );
        $dao->setConverter( $converter );
        $dao->setRelation( $relation );
        return $dao;
    }

    /**
     * @param Converter $converter
     */
    public function setConverter( $converter )
    {
        $converter->setEntityClass( $this->entityClass );
        $converter->setColumns(     $this->columns );
        $converter->setMutator( $this );
        $this->convert = $converter;
    }

    /**
     * @param Relation $relation
     */
    public function setRelation( $relation )
    {
        $relation->setDao( $this );
        $this->relation = $relation;
        $this->setHook( $relation );
    }

    // +----------------------------------------------------------------------+
    //  entity related methods
    // +----------------------------------------------------------------------+
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
            $id = Magic::get( $entity, $this->primaryKey );
            $this->update( $id, $data );
        } else {
            $id = $this->insert( $data );
            Magic::set( $entity, $this->primaryKey, $id );
            $this->hash( $entity );
        }
        $this->hooks( 'saved', $data );
    }

    /**
     * @param null|string $id
     * @param null|string $column
     * @return object[]
     */
    public function load( $id=null, $column=null )
    {
        $this->hooks( 'loading', $id );
        $this->setId( $id, $column );
        $list = $this->select();
        foreach( $list as $key => $data ) {
            $list[$key] = $entity = $this->convert->toEntity( $data );
            $this->hash($entity);
        }
        $this->hooks( 'loaded', $list );
        if( $id && !$column ) { // load by primaryKey. should have one result.
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
        $key = Magic::get( $entity, $this->primaryKey );
        $this->hooks( 'removing', $key );
        $this->delete($key);
        $this->deletedEntity[] = spl_object_hash($entity);
        $this->hooks( 'removed', $key );
    }
    // +----------------------------------------------------------------------+
    //  converting entity to/from array data.
    // +----------------------------------------------------------------------+
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

    /**
     * @param object $entity
     * @param string $name
     * @return RelationAbstract
     */
    public function relate( $entity, $name ) {
        return $this->relation->loadRelation( $entity, $name );
    }
    // +----------------------------------------------------------------------+
}