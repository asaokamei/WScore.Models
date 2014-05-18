<?php
namespace WScore\Models\Dao\Relation;

abstract class RelationAbstract
{
    /**
     * information about the relationship.
     * 
     * @var array
     */
    protected $info = array();

    /**
     * source of entity. 
     * 
     * @var object|array
     */
    protected $source;
    
    /**
     * target entity(ies). 
     * 
     * @var array|object|object[]
     */
    protected $target;

    /**
     * @param $source
     */
    public function setSource( $source )
    {
        $this->source = $source;
    }

    /**
     * @param $target
     */
    public function setTarget( $target )
    {
        $this->target = $target;
    }

    /**
     * @return bool
     */
    abstract public function save();
}