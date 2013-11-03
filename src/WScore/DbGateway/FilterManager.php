<?php

namespace WScore\DbGateway;

use WScore\DbGateway\Filter\FilterInterface;

class FilterManager
{
    /**
     * @var Gateway
     */
    public $model;
    
    /**
     * @var FilterInterface[]
     */
    public $filters = array();

    /**
     * @param Gateway $model
     * @return $this
     */
    public function setModel( $model )
    {
        $this->model = $model;
        return $this;
    }

    /**
     * @param FilterInterface $filter
     * @return $this
     */
    public function addFilter( $filter )
    {
        $name = get_class( $filter );
        $this->filters[ $name ] = $filter;
        return $this;
    }

    /**
     * @param null|FilterInterface $filter
     * @return $this
     */
    public function clearFilters( $filter=null ) 
    {
        if( $filter ) {
            $name = get_class( $filter );
            if( isset( $this->filters[ $name ] ) ) unset( $this->filters[ $name ] );
        } else {
            $this->filters = array();
        }
        return $this;
    }

    /**
     * @param string                   $event
     * @param                          $data
     */
    public function apply( $event, &$data )
    {
        if( empty( $this->filters ) ) return;
        foreach( $this->filters as $filter ) {
            if( !$filter instanceof FilterInterface ) continue;
            $filter->setModel( $this->model );
            $data = $filter->apply( $event, $data );
        }
        return;
    }
}