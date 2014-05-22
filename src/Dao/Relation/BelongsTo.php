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
        $this->info = array(
            'myKey'     => $myKey,
            'targetDao' => $targetDao,
            'targetKey' => $targetKey,
        );
    }

    /**
     * @return array
     */
    protected function getInfo()
    {
        static $initialized = false;
        if( !$initialized ) {
            $targetKey = Dao::dao($this->info['targetDao'])->getKeyName();
            $this->info['targetKey'] = $this->info['targetKey'] ?: $targetKey;
            $this->info['myKey'] = $this->info['myKey'] ?: $targetKey;
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
        $id = Magic::get( $this->target, $info['targetKey'] );
        if( $id ) {
            Magic::set( $this->source, $info['myKey'], $id );
            $this->isLinked = true;
        }
        return $this->isLinked();
    }

    /**
     * @return array
     */
    public function load()
    {
        $info = $this->getInfo();
        $dao = Dao::dao( $info['targetDao'] );
        $key = $info[ 'targetKey' ];
        $id  = Magic::get( $this->source, $info['myKey'] );
        if( $target = $dao->load( $id, $key ) ) {
            if( count( $target ) ) {
                $target = $target[0];
            }
            $this->target = $target;
        }
        Magic::set( $this->source, $this->name, $this->target );
        $this->isLinked = true;
        return $this->target;
    }
}