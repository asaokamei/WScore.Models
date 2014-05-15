<?php
namespace WScore\Models\Query;

use Illuminate\Database\Query\Builder;
use WScore\Models\DaoEntity;

/**
 * Class Eloquent
 * a dummy class for DaoEntity/DaoArray.
 *
 * @package WScore\Models\Query
 */
class Eloquent extends Builder
{
    /**
     * @var DaoEntity
     */
    protected $_wscore_dao;

    /**
     * @param DaoEntity $dao
     */
    public function setWScoreDao( $dao )
    {
        $this->_wscore_dao = $dao;
    }

    /**
     * @param array $columns
     * @return array
     */
    public function select( $columns=array('*') )
    {
        return $this->_wscore_dao->select($columns);
    }

    /**
     * @param array $values
     * @return int
     */
    public function update( array $values )
    {
        return $this->_wscore_dao->update($values);
    }

    /**
     * @param array $values
     * @return bool
     */
    public function insert( array $values )
    {
        return $this->_wscore_dao->insert($values);
    }

    /**
     * @param null|string|int $id
     * @return int
     */
    public function delete( $id=null )
    {
        return $this->_wscore_dao->delete($id);
    }

    /**
     * @param null|string|int $id
     * @return object[]
     */
    public function load( $id=null )
    {
        return $this->_wscore_dao->load($id);
    }

    /**
     * @param object $entity
     */
    public function save($entity)
    {
        return $this->_wscore_dao->save($entity);
    }
}