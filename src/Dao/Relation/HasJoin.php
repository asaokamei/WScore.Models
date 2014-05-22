<?php
namespace WScore\Models\Dao\Relation;

use WScore\Models\Dao;
use WScore\Models\Entity\Magic;

/**
 * @property mixed dao
 */
class HasJoin extends RelationAbstract
{

    /**
     * @param string $name
     * @param string $target
     */
    public function __construct( $name, $target )
    {
        $this->name = $name;
        $this->info = array(
            'targetDao'    => $target,
        );
    }

    /**
     * @param      $key
     * @param null $joinKey
     * @return $this
     */
    public function targetKey( $key, $joinKey=null )
    {
        $this->info = array_merge(
            $this->info,
            [
                'targetKey'  => $key,
                'joinTargetKey' => $joinKey,
            ]
        );
        return $this;
    }

    /**
     * @param $joinBy
     * @return $this
     */
    public function joinBy($joinBy)
    {
        $this->info = array_merge(
            $this->info,
            [
                'joinBy'  => $joinBy,
            ]
        );
        return $this;
    }

    /**
     * @param      $key
     * @param null $joinKey
     * @return $this
     */
    public function sourceKey( $key, $joinKey=null )
    {
        $this->info = array_merge(
            $this->info,
            [
                'sourceKey'     => $key,
                'joinSourceKey' => $joinKey,
            ]
        );
        return $this;
    }

    /**
     * @return array
     */
    public function getInfo()
    {
        if( !$this->initialized ) {
            $info = &$this->info;
            $target = $info['targetDao'];
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
        return $this->info;
    }

    /**
     * @return bool
     */
    public function relate()
    {
        $info = $this->getInfo();
        $join = Dao::dao( $info['joinBy']);
        $sourceId = Magic::get( $this->source, $info['sourceKey']);
        $targetId = Magic::get( $this->target, $info['targetKey']);
        
        $join->delete( $sourceId, $info['joinSourceKey'] );
        foreach( $targetId as $id ) {
            $join->insert( [
                $info['joinSourceKey'] => $sourceId,
                $info['joinTargetKey'] => $id,
            ] );
        }
        return $this->isLinked = true;
    }

    /**
     * loads related data from the database into the entity data.
     *
     * @return null|array|object
     */
    public function load()
    {
        $info = $this->getInfo();
        $join = Dao::dao( $info['joinBy']);
        if( !$sourceId = Magic::get( $this->source, $info['sourceKey'] ) ) {
            return false;
        }
        if( !$joinData = $join->load( $sourceId, $info['joinSourceKey'] ) ) {
            return false;
        }
        $targetId = Magic::get( $joinData, $info['joinTargetKey'] );
        $target = Dao::dao( $info['targetDao'] );
        $this->target = $target->load( $targetId, $info['targetKey'] );
        return $this->isLinked = true;
    }
}