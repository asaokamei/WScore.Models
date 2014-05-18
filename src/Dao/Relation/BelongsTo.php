<?php
namespace WScore\Models\Dao\Relation;

use WScore\Models\Dao;
use WScore\Models\Entity\Magic;

class BelongsTo extends RelationAbstract
{
    protected $info = array();

    /**
     * @param string      $targetDao
     * @param string|null $targetKey
     * @param string|null $myKey
     */
    public function __construct( $targetDao, $targetKey=null, $myKey=null )
    {
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
}