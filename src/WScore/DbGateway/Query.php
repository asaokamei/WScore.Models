<?php
namespace WScore\DbGateway;

class Query
{
    /**
     * @var \WScore\DbAccess\Query
     */
    public $query;

    /**
     * @param $table
     * @param $id_name
     * @return \WScore\DbAccess\Query
     */
    public function table( $table, $id_name )
    {
        return $this->query->table( $table, $id_name );
    }

    /**
     * @param string $column
     * @param string $value
     * @param string $type
     */
    public function condition( $column, $value, $type='eq' )
    {
        $this->query->col( $column )->$type( $value );
    }

    /**
     * @param string $type
     */
    public function execType( $type )
    {
        $this->query->execType( $type );
    }

    /**
     * @return \WScore\DbAccess\Query
     */
    public function exec()
    {
        return $this->query->exec();
    }
    
    public function lastId()
    {
        return $this->query->lastId();
    }
}