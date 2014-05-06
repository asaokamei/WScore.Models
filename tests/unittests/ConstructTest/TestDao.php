<?php
namespace tests\ConstructTest;

use WScore\DbGateway\Dao;

class TestDao extends Dao
{
    protected $created_date = 'creation_date';

    /**
     * testing toObject
     *
     * @param $data
     * @param $name
     * @return array|object
     */
    public function callToObject( &$data, $name=null  ) {
        return $this->toObject( $data, $name );
    }
    /**
     * testing updateTimeStamps
     *
     * @param      $data
     * @param bool $insert
     */
    public function callUpdateTimeStamps( &$data, $insert=false ) {
        $this->updateTimeStamps( $data, $insert );
    }

    /**
     * testing toString
     * @param $data
     * @return array|mixed|string
     */
    public function callToString( $data ) {
        return $this->toString( $data );
    }
    
    public function _setAny( $name, $value ) {
        $this->$name = $value;
    }
    
    public function _getAny( $name ) {
        if( isset( $this->$name ) ) {
            return $this->$name;
        }
        return null;
    }
}