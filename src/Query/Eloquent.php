<?php
namespace WScore\DbGateway\Query;

use Illuminate\Database\Query\Builder;
use WScore\DbGateway\DaoArray;

class Eloquent extends Builder
{
    /**
     * @var DaoArray
     */
    protected $_wscore_dao;

    /**
     * @param DaoArray $dao
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
}