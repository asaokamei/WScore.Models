<?php
namespace WScore\Models\Dao\Relation;

use WScore\Models\Dao;

/**
 * @property mixed dao
 */
class HasMany extends RelationAbstract
{

    /**
     * @param string $target
     * @param string|null $targetKey
     * @param string|null $myKey
     */
    public function __construct( $target, $targetKey=null, $myKey=null )
    {
        $myKey     = $myKey?: $this->dao->getKeyName();
        $targetKey = $targetKey?: Dao::dao($target)->getKeyName();
        $this->info = array(
            'myKey'     => $myKey,
            'target'    => $target,
            'targetKey' => $targetKey,
        );
    }
    
    /**
     * @return bool
     */
    public function save()
    {
        // TODO: Implement save() method.
    }
}