<?php
namespace WScore\Models\Dao\Relation;

use WScore\Models\Dao;
use WScore\Models\Entity\Magic;

/**
 * @property mixed dao
 */
class HasMany extends RelationAbstract
{

    /**
     * @param string $targetDao
     * @param string|null $targetKey
     * @param string|null $myKey
     */
    public function __construct( $targetDao, $targetKey=null, $myKey=null )
    {
        $myKey     = $myKey?: $this->myPrimaryKey;
        $targetKey = $targetKey?: Dao::dao($targetDao)->getKeyName();
        $this->info = array(
            'myKey'     => $myKey,
            'targetDao' => $targetDao,
            'targetKey' => $targetKey,
        );
    }
    
    /**
     * @return bool
     */
    public function relate()
    {
        $id = Magic::get( $this->source, $this->info['myKey'] );
        if( $id ) {
            Magic::set( $this->target, $this->info['targetKey'], $id );
            $this->isLinked = true;
        }
        return $this->isLinked();
    }

    /**
     * loads related data from the database into the entity data.
     *
     * @return null|array|object
     */
    public function load()
    {
        $dao = Dao::dao( $this->info['targetDao'] );
        $key = $this->info[ 'targetKey' ];
        $id  = Magic::get( $this->source, $this->info['myKey'] );
        if( $target = $dao->load( $id, $key ) ) {
            $this->target = $target;
        }
        Magic::set( $this->source, $this->name, $this->target );
        $this->isLinked = true;
        return $this->target;
    }
}