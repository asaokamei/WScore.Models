<?php
namespace WScore\Models\Dao\Relation;

abstract class RelationAbstract
{
    /**
     * name of the relation in the entity.
     *
     * @var
     */
    protected $name;

    protected $myDaoName;

    protected $myPrimaryKey;

    protected $initialized = false;
    
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
     * turn to true when relation is linked.
     * 
     * @var bool
     */
    protected $isLinked = false;
    
    /**
     * @param $source
     */
    public function setSource( & $source )
    {
        $this->source = & $source;
    }

    /**
     * @param $target
     */
    public function setTarget( $target )
    {
        $this->target = $target;
        $this->isLinked = false;
    }

    /**
     * @return array
     */
    abstract public function getInfo();
    
    /**
     * @return bool
     */
    public function isLinked() {
        return $this->isLinked;
    }

    /**
     * @param object|object[] $target
     * @return bool
     */
    abstract public function relate($target=null);

    /**
     * loads related data from the database into the entity data.
     *
     * @return null|array|object
     */
    abstract public function load();

    /**
     * @param mixed $myDaoName
     */
    public function setMyDaoName( $myDaoName )
    {
        $this->myDaoName = $myDaoName;
    }

    /**
     * @param mixed $myPrimaryKey
     */
    public function setMyKeyName( $myPrimaryKey )
    {
        $this->myPrimaryKey = $myPrimaryKey;
    }
}