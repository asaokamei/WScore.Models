<?php
namespace WScore\DbGateway\Filter;

use WScore\DbGateway\QueryInterface;

class SetTimeUpdatedAt extends FilterAbstract
{
    use SetTimeTrait;

    /**
     * @param QueryInterface $query
     */
    protected function onUpdate( $query )
    {
        $this->setTime( $query, $this->column_name );
    }

}