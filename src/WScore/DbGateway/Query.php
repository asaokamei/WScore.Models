<?php
namespace WScore\DbGateway;

class Query implements QueryInterface
{
    /**
     * @var \WScore\DbAccess\Query
     */
    public $query;

    // +----------------------------------------------------------------------+
    /**
     * @param $query
     */
    public function setQuery( $query ) {
        $this->query = $query;
    }

    /**
     * @return \WScore\DbAccess\Query
     */
    public function q() {
        return $this->query;
    }

    // +----------------------------------------------------------------------+
    //  SQL query adapter
    // +----------------------------------------------------------------------+
    /**
     * @param $data
     * @return $this
     */
    public function setData( $data ) 
    {
        $this->query->values( $data );
        return $this;
    }

    /**
     * @param $name
     * @return mixed|null
     */
    public function getValue( $name )
    {
        return $this->query->getValue( $name );
    }

    /**
     * @param $name
     * @param $value
     */
    public function setValue( $name, $value )
    {
        $this->query->setValue( $name, $value );
    }
    
    /**
     * gets the last inserted ID.
     *
     * @return string
     */
    public function getLastId()
    {
        return $this->query->lastId();
    }

    /**
     * sets table name and primary key. 
     * 
     * @param $table
     * @param $id_name
     * @return \WScore\DbAccess\Query
     */
    public function setTable( $table, $id_name )
    {
        return $this->query->table( $table, $id_name );
    }

    /**
     * sets where condition.
     *
     * @param string $column
     * @param string $value
     * @param string $type
     * @return $this
     */
    public function condition( $column, $value, $type='eq' )
    {
        $this->query->col( $column )->$type( $value );
        return $this;
    }

    /**
     * sets execution type.
     *
     * @param string $type
     * @return $this
     */
    public function setExecType( $type )
    {
        $this->query->execType( $type );
        return $this;
    }

    /**
     * executes the query.  
     * 
     * @return \WScore\DbAccess\Query
     */
    public function execute()
    {
        return $this->query->exec();
    }

    /**
     * @return mixed
     */
    public function getResult()
    {
        return $this->query->getStmt();
    }
    // +----------------------------------------------------------------------+
}