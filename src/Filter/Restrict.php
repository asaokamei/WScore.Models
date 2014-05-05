<?php
namespace WScore\DataMapper\Filter;

use WScore\DbGateway\QueryInterface;

/**
 * Class Restrict
 *
 * @package WScore\DataMapper\Filter
 */
class Restrict extends FilterAbstract
{
    protected $list;

    /**
     * @param array $list
     */
    public function setProperties( $list ) 
    {
        $this->list = $list;
    }
    
    /**
     * @param QueryInterface $query
     */
    protected function onInsert( $query )
    {
        $this->restrict( $query );
    }

    /**
     * @param QueryInterface $query
     */
    protected function onUpdate( $query )
    {
        $this->restrict( $query );
    }

    /**
     * @param QueryInterface $query
     */
    protected function restrict( $query )
    {
        $values = $query->getValue();
        if ( empty( $values ) ) return;
        foreach ( $values as $name => $val ) {
            if ( !in_array( $name, $this->list ) ) {
                $query->delValue( $name );
            }
        }
    }
}
