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
     * @param string $name
     * @param string $targetDao
     * @param string|null $targetKey
     * @param string|null $myKey
     */
    public function __construct( $name, $targetDao, $targetKey=null, $myKey=null )
    {
        $this->name = $name;
        $this->info = array(
            'myKey'     => $myKey,
            'targetDao' => $targetDao,
            'targetKey' => $targetKey,
        );
    }

    /**
     * @return array
     */
    public function getInfo()
    {
        if( !$this->initialized ) {
            $this->info['targetKey'] = $this->info['targetKey'] ?: $this->myPrimaryKey;
            $this->info['myKey'] = $this->info['myKey'] ?: $this->myPrimaryKey;
            $initialized = true;
        }
        return $this->info;
    }
    
    /**
     * @return bool
     */
    public function relate()
    {
        $info = $this->getInfo();
        $id = Magic::get( $this->source, $info['myKey'] );
        if( $id ) {
            Magic::set( $this->target, $info['targetKey'], $id );
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
        $info = $this->getInfo();
        $dao = Dao::dao( $info['targetDao'] );
        $key = $info[ 'targetKey' ];
        $id  = Magic::get( $this->source, $info['myKey'] );
        if( $target = $dao->load( $id, $key ) ) {
            $this->target = $target;
        }
        Magic::set( $this->source, $this->name, $this->target );
        $this->isLinked = true;
        return $this->target;
    }
}