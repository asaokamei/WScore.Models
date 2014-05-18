<?php
namespace WScore\Models\Dao\Relation;

use WScore\Models\Dao;

class BelongsTo extends RelationAbstract
{
    protected $info = array();

    /**
     * @param string      $target
     * @param string|null $targetKey
     * @param string|null $myKey
     */
    public function __construct( $target, $targetKey=null, $myKey=null )
    {
        $targetKey = $targetKey?: Dao::dao($target)->getKeyName();
        $this->info = array(
            'myKey'     => $myKey ?: $targetKey,
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