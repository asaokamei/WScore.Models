<?php

namespace WScore\DataMapper;

use Closure;

class FilterManager
{
    /**
     * @var Model\Model
     */
    public $model;
    
    /**
     * @var Filter\FilterInterface[][]|\Closure[][]
     */
    public $filters = array();

    /**
     * @var Filter\FilterInterface[]|Closure[]
     */
    public $rules = array();


    /**
     * @param string                 $event
     * @param Filter\FilterInterface $filter
     * @return $this
     */
    public function addFilter( $event, $filter )
    {
        $this->filters[ $event ][] = $filter;
        return $this;
    }

    /**
     * @param Filter\FilterInterface $rule
     * @return $this
     */
    public function addRule( $rule )
    {
        $name = get_class( $rule );
        $this->rules[ $name ] = $rule;
        return $this;
    }

    /**
     * @return $this
     */
    public function clearRules() {
        $this->rules = array();
        return $this;
    }

    /**
     * @param Model\Model $model
     * @return $this
     */
    public function setModel( $model ) 
    {
        $this->model = $model;
        return $this;
    }

    /**
     * @param string $event
     * @param mixed $data
     * @return mixed
     */
    public function event( $event, $data )
    {
        $data   = $this->apply( $this->rules, $event, $data );
        if( !isset( $this->filters[ $event ] ) ) return $data;
        $data   = $this->apply( $this->filters[ $event ], $event, $data );
        
        return $data;
    }

    /**
     * @param Filter\FilterInterface[] $filters
     * @param                          $event
     * @param                          $data
     */
    private function apply( $filters, $event, $data )
    {
        if( !$filters || empty( $filters ) ) return $data;
        foreach( $filters as $filter ) {
            if( !$filter instanceof Filter\FilterInterface ) continue;
            $filter->setModel( $this->model );
            $data = $filter->apply( $event, $data );
        }
        return $data;
    }
}