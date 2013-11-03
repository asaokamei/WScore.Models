<?php
namespace WScore\DbGateway\Filter;

use WScore\DbGateway\QueryInterface;

class SetTimeCreatedAt extends FilterAbstract
{
    protected $column_name;

    protected $current_time;
    
    /**
     * sets name for created_at column.
     * 
     * @param string $name
     */
    public function setColumnName( $name ) 
    {
        $this->column_name = $name;
    }
    
    public function setCurrentTime( $now )
    {
        $this->current_time = $now;
    }
    
    /**
     * @param QueryInterface $query
     */
    protected function onInsert( $query )
    {
        $this->setTime( $query, $this->column_name );
    }

    /**
     * @param QueryInterface $query
     * @param string         $column
     */
    protected function setTime( $query, $column )
    {
        if( !$query->getValue( $column ) ) return;
        if( !$this->current_time ) {
            $this->current_time = new \DateTime('now');
            $this->current_time = $this->current_time->format( 'Y-m-d H:i:s' );
        }
        $query->setValue( $column, $this->current_time );
    }
}