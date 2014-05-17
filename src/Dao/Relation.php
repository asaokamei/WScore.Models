<?php
namespace WScore\Models\Dao;

use WScore\Models\Dao;
use WScore\Models\DaoArray;

class Relation
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
     * @param DaoArray $dao
     */
    public function setDao($dao) {
        $this->dao = $dao;
    }

    /**
     * @param string $name
     * @param string $target
     * @param string|null $targetKey
     * @param string|null $myKey
     */
    public function belongsTo( $name, $target, $targetKey=null, $myKey=null )
    {
        $targetKey = $targetKey?: Dao::dao($target)->getKeyName();
        $this->relations[$name] = array(
            'type' => 'BelongsTo',
            'target' => $target,
            'targetKey' => $targetKey,
            'myKey'     => $myKey ?: $targetKey,
        );
    }
    
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
}