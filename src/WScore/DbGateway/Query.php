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
    public function values( $data ) 
    {
        $this->query->values( $data );
        return $this;
    }
    
    /**
     * gets the last inserted ID.
     *
     * @return string
     */
    public function lastId()
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
    public function table( $table, $id_name )
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
    public function execType( $type )
    {
        $this->query->execType( $type );
        return $this;
    }

    /**
     * executes the query.  
     * 
     * @return \WScore\DbAccess\Query
     */
    public function exec()
    {
        return $this->query->exec();
    }

    /**
     * @return mixed
     */
    public function result()
    {
        return $this->query->getStmt();
    }
    // +----------------------------------------------------------------------+
}