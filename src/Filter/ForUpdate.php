<?php

namespace WScore\DbGateway\Filter;

use WScore\DbGateway\QueryInterface;

class ForUpdate extends FilterAbstract
{
    /**
     * @param QueryInterface $query
     * @return \WScore\DbAccess\Query
     */
    public function onQuery( $query )
    {
        $query->forUpdate();
    }
}