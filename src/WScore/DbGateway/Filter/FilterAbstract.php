<?php
namespace WScore\DataMapper\Filter;

use WScore\DataMapper\Model\Model;

abstract class FilterAbstract implements FilterInterface
{
    /**
     * @var Model
     */
    protected $model;

    /**
     * @param Model $model
     */
    public function setModel( $model ) {
        $this->model = $model;
    }

    /**
     * @param string $event
     * @param mixed  $data
     * @return mixed
     */
    public function apply( $event, $data )
    {
        $method = 'on' . ucwords( $event );
        if( method_exists( $this, $method ) ) {
            return $this->$method( $data );
        }
        return $data;
    }
}