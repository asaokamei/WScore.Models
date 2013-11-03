<?php
namespace WScore\DbGateway\Filter;

use WScore\DbGateway\Gateway;

abstract class FilterAbstract implements FilterInterface
{
    /**
     * @var Gateway
     */
    protected $model;

    /**
     * @param Gateway $model
     */
    public function setModel( $model ) {
        $this->model = $model;
    }

    /**
     * @param string $event
     * @param mixed  $data
     * @return mixed
     */
    public function apply( $event, &$data )
    {
        $method = 'on' . ucwords( $event );
        if( method_exists( $this, $method ) ) {
            return $this->$method( $data );
        }
        return $data;
    }
}