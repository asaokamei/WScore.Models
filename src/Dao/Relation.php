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
     * @var string
     */
    protected $currName = null;
    
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
     * @param string      $target
     * @param string|null $targetKey
     * @param string|null $myKey
     * @return BelongsTo
     */
    public function belongsTo( $name, $target, $targetKey=null, $myKey=null )
    {
        $this->currName = $name;
        $relation = new BelongsTo( $target, $targetKey, $myKey );
        $this->relations[$name] = $relation;
        return $relation;
    }

    /**
     * @param string      $name
     * @param string      $target
     * @param string|null $targetKey
     * @param string|null $myKey
     * @return \WScore\Models\Dao\Relation\HasMany
     */
    public function hasMany( $name, $target, $targetKey=null, $myKey=null )
    {
        $this->currName = $name;
        $relation = new HasMany( $target, $targetKey, $myKey );
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
        $this->currName = $name;
        $relation = new HasJoin( $target );
        $this->relations[$name] = $relation;
        return $relation;
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
            $target = Magic::get( $data, $name );
            $relation->setSource( $data );
            $relation->setTarget( $target );
            if( !$relation->relate() ) {
                // do nothing. maybe linked in saved filter. 
            }
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
            $relation = $this->relations[$name];
            if( $relation->isLinked() ) continue;
            if( !$relation->relate() ) {
                throw new \RuntimeException('Cannot relate data');
            }
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

    // +----------------------------------------------------------------------+
}