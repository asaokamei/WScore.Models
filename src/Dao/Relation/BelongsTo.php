<?php
namespace WScore\Models\Dao\Relation;

use WScore\Models\Dao;
use WScore\Models\Entity\Magic;

class BelongsTo extends RelationAbstract
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
        $targetKey = $targetKey?: Dao::dao($targetDao)->getKeyName();
        $this->info = array(
            'myKey'     => $myKey ?: $targetKey,
            'targetDao' => $targetDao,
            'targetKey' => $targetKey,
        );
    }

    /**
     * @return bool
     */
    public function relate()
    {
        $id = Magic::get( $this->target, $this->info['targetKey'] );
        if( $id ) {
            Magic::set( $this->source, $this->info['myKey'], $id );
            $this->isLinked = true;
        }
        return $this->isLinked();
    }

    /**
     * @return array
     */
    public function load()
    {
        $dao = Dao::dao( $this->info['targetDao'] );
        $key = $this->info[ 'targetKey' ];
        $id  = Magic::get( $this->source, $this->info['myKey'] );
        if( $target = $dao->load( $id, $key ) ) {
            $this->target = $target[0];
        }
        Magic::set( $this->source, $this->name, $this->target );
        return $this->target;
    }
}