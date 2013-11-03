<?php

namespace WScore\DataMapper\Filter;

class ForUpdate extends FilterAbstract
{
    /**
     * @param \WScore\DbAccess\Query $query
     * @return \WScore\DbAccess\Query
     */
    public function onQuery( $query )
    {
        $query->forUpdate();
        return $query;
    }
}