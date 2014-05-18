<?php
namespace WScore\Models\Dao;

use WScore\Models\Dao;
use WScore\Models\DaoArray;

class Relation implements RelationHasJoinInterface, RelationSetupInterface
{
    /**
     * @var DaoArray
     */
    protected $dao;

    /**
     * @var array
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

    /**
     * @param string $name
     * @return array
     * @throws \RuntimeException
     */
    protected function getRelation( $name=null )
    {
        if( !$name ) $name = $this->currName;
        if( array_key_exists($name, $this->relations)) {
            return $this->relations[$name];
        }
        throw new \RuntimeException('No such relation: '.$name);
    }

    // +----------------------------------------------------------------------+
    //  set up relations (simple ones).
    // +----------------------------------------------------------------------+
    /**
     * @param string $name
     * @param string $target
     * @param string|null $targetKey
     * @param string|null $myKey
     */
    public function belongsTo( $name, $target, $targetKey=null, $myKey=null )
    {
        $this->currName = $name;
        $targetKey = $targetKey?: Dao::dao($target)->getKeyName();
        $this->relations[$name] = array(
            'type'      => 'BelongsTo',
            'myKey'     => $myKey ?: $targetKey,
            'target'    => $target,
            'targetKey' => $targetKey,
        );
    }

    /**
     * @param string $name
     * @param string $target
     * @param string|null $targetKey
     * @param string|null $myKey
     */
    public function hasMany( $name, $target, $targetKey=null, $myKey=null )
    {
        $this->currName = $name;
        $myKey     = $myKey?: $this->dao->getKeyName();
        $targetKey = $targetKey?: Dao::dao($target)->getKeyName();
        $this->relations[$name] = array(
            'type'      => 'HasMany',
            'myKey'     => $myKey,
            'target'    => $target,
            'targetKey' => $targetKey,
        );
    }
    
    // +----------------------------------------------------------------------+
    //  set up many to many relationship. 
    // +----------------------------------------------------------------------+
    /**
     * @param      $name
     * @param      $target
     * @return RelationHasJoinInterface
     */
    public function hasJoin( $name, $target )
    {
        $this->currName = $name;
        $this->relations[$name] = array(
            'type'      => 'HasJoin',
            'target'    => $target,
        );
        return $this;
    }

    /**
     * @param string $name
     */
    protected function setupHasJoin( $name=null )
    {
        $info = $this->getRelation($name);
        $target = $info['target'];
        if( !isset( $info['joinBy'] ) ) {
            $joinBy = [$this->dao->getTable(), Dao::dao($target)->getTable()];
            sort( $joinBy );
            $info['joinBy'] = implode( '_', $joinBy );
        }
        if( !isset( $info['targetKey'] ) ) {
            $info['targetKey'] = Dao::dao($target)->getKeyName();
        }
        if( !isset( $info['targetBy'] ) ) {
            $info['targetBy'] = $info['targetKey'];
        }
        if( !isset( $info['joinTargetKey'] ) ) {
            $info['joinTargetKey'] = $info['targetKey'];
        }
        if( !isset( $info['sourceKey'] ) ) {
            $info['sourceKey'] = $this->dao->getKeyName();
        }
        if( !isset( $info['joinSourceKey'] ) ) {
            $info['joinSourceKey'] = $info['sourceKey'];
        }
    }

    /**
     * @param      $key
     * @param null $joinKey
     * @return RelationHasJoinInterface
     */
    public function targetKey( $key, $joinKey=null )
    {
        $this->relations[$this->currName] = array_merge(
            $this->relations[$this->currName],
            [
                'targetKey'  => $key,
                'joinTargetKey' => $joinKey,
            ]
        );
        return $this;
    }

    /**
     * @param $joinBy
     * @return RelationHasJoinInterface
     */
    public function joinBy($joinBy)
    {
        $this->relations[$this->currName] = array_merge(
            $this->relations[$this->currName],
            [
                'joinBy'  => $joinBy,
            ]
        );
        return $this;
    }

    /**
     * @param      $key
     * @param null $joinKey
     * @return RelationHasJoinInterface
     */
    public function sourceKey( $key, $joinKey=null )
    {
        $this->relations[$this->currName] = array_merge(
            $this->relations[$this->currName],
            [
                'sourceKey'     => $key,
                'joinSourceKey' => $joinKey,
            ]
        );
        return $this;
    }

    // +----------------------------------------------------------------------+
    //  on hook filters
    // +----------------------------------------------------------------------+
    /**
     * converts related entity objects into foreign key. 
     * 
     * @param $data
     */
    public function onSaving( $data )
    {
        
    }

    /**
     * loads related entities. 
     * 
     * @param $list
     */
    public function onLoaded( $list )
    {
        
    }

    // +----------------------------------------------------------------------+
}