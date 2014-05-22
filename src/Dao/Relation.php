<?php
namespace WScore\Models\Dao;

use WScore\Models\Dao;
use WScore\Models\Dao\Relation\BelongsTo;
use WScore\Models\Dao\Relation\HasJoin;
use WScore\Models\Dao\Relation\HasMany;
use WScore\Models\Dao\Relation\RelationAbstract;
use WScore\Models\DaoArray;
use WScore\Models\Entity\Magic;

class Relation
{
    /**
     * @var DaoArray
     */
    protected $dao;

    /**
     * @var RelationAbstract[]
     */
    protected $relations = array();

    /**
     * @var bool
     */
    protected $setUpDone = false;

    /**
     * list of relations based on entity hash.
     *
     * @var RelationAbstract[][]
     */
    protected $hashed = array();

    /**
     * @param DaoArray $dao
     */
    public function setDao($dao) {
        $this->dao = $dao;
    }

    // +----------------------------------------------------------------------+
    //  set up relations.
    // +----------------------------------------------------------------------+
    /**
     * @param string      $name
     * @param string      $targetDao
     * @param string|null $targetKey
     * @param string|null $myKey
     * @return BelongsTo
     */
    public function belongsTo( $name, $targetDao, $targetKey=null, $myKey=null )
    {
        $relation = new BelongsTo( $name, $targetDao, $targetKey, $myKey );
        $relation->setMyDaoName( $this->dao->getDaoName() );
        $relation->setMyKeyName( $this->dao->getKeyName() );
        $this->relations[$name] = $relation;
        return $relation;
    }

    /**
     * @param string      $name
     * @param string      $target
     * @param string|null $targetKey
     * @param string|null $myKey
     * @return HasMany
     */
    public function hasMany( $name, $target, $targetKey=null, $myKey=null )
    {
        $relation = new HasMany( $name, $target, $targetKey, $myKey );
        $relation->setMyDaoName( $this->dao->getDaoName() );
        $relation->setMyKeyName( $this->dao->getKeyName() );
        $this->relations[$name] = $relation;
        return $relation;
    }
    
    /**
     * @param      $name
     * @param      $target
     * @return HasJoin
     */
    public function hasJoin( $name, $target )
    {
        $relation = new HasJoin( $name, $target );
        $relation->setMyDaoName( $this->dao->getDaoName() );
        $relation->setMyKeyName( $this->dao->getKeyName() );
        $this->relations[$name] = $relation;
        return $relation;
    }

    /**
     * just before using any of the relation is used, 
     * set up all the relations. Setting up the relation 
     * requires all the dao to be constructed... 
     */
    protected function setUpRelations()
    {
        if( $this->setUpDone ) return;
        foreach( $this->relations as $relation ) {
            $relation->getInfo();
        }
    }

    // +----------------------------------------------------------------------+
    //  on hook filters
    // +----------------------------------------------------------------------+
    /**
     * converts related entity objects into foreign key.
     *
     * @param $data
     * @throws \RuntimeException
     * @return array|object
     */
    public function onSavingFilter( $data )
    {
        foreach( $this->relations as $name => $relation ) {
            $this->link( $data, $name, false );
        }
        return $data;
    }

    /**
     * try to convert related entity objects, after the main entity is saved.
     * 
     * @param $data
     * @return mixed
     * @throws \RuntimeException
     */
    public function onSavedFilter( $data )
    {
        foreach( $this->relations as $name => $relation ) {
            $this->link( $data, $name, true );
        }
        return $data;
    }

    /**
     * @param object $data
     * @param string $name
     * @param bool $throw
     * @throws \RuntimeException
     * @return mixed
     */
    protected function link( &$data, $name, $throw=true )
    {
        $target = Magic::get( $data, $name );
        $relation = $this->findRelation( $data, $name );

        if( !$relation ) {
            // related data were not loaded, so far.
            if( !$target ) {
                // new related data were set, either.
                // ignore this case.
                return $data;
            }
        }
        if( !$this->relate( $data, $name, $target ) ) {
            if( $throw ) {
                throw new \RuntimeException('Cannot relate data');
            }
            // do nothing. maybe linked in saved filter.
        }
        return $data;
    }

    /**
     * loads related entities. 
     * 
     * @param $list
     * @return array
     */
    public function onLoadedFilter( $list )
    {
        return $list;
    }

    /**
     * relates the entity to target for relation $name.
     *
     * @param $entity
     * @param $name
     * @param $target
     * @return bool
     */
    public function relate( $entity, $name, $target )
    {
        $this->setUpRelations();
        $relation = $this->loadRelation( $entity, $name );
        $relation->setTarget( $target );
        return $relation->relate();
    }

    /**
     * @param $entity
     * @param $name
     * @return RelationAbstract
     * @throws \RuntimeException
     */
    public function loadRelation( $entity, $name )
    {
        if( !array_key_exists( $name, $this->relations ) ) {
            throw new \RuntimeException( 'No such relation: '.$name );
        }
        if( !$relation = $this->findRelation( $entity, $name ) ) {
            $relation = clone( $this->relations[$name] );
            $relation->setSource( $entity );
            $this->saveRelation( $entity, $name, $relation );
        }
        return $relation;
    }

    /**
     * @param $entity
     * @param $name
     * @return null|RelationAbstract
     */
    protected function findRelation( $entity, $name )
    {
        if( is_array( $entity ) ) {
            return null;
        }
        if( !array_key_exists( $hash = spl_object_hash( $entity ), $this->hashed ) ) {
            return null;
        }
        if( !array_key_exists( $name, $this->hashed[$hash] ) ) {
            return null;
        }
        return $this->hashed[$hash][$name];
    }

    /**
     * @param $entity
     * @param $name
     * @param $relation
     */
    protected function saveRelation( $entity, $name, $relation )
    {
        if( is_array( $entity ) ) {
            return;
        }
        $hash = spl_object_hash( $entity );
        $this->hashed[$hash][$name] = $relation;
    }
    // +----------------------------------------------------------------------+
}