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
     * @return array|object
     */
    public function callToObject( &$data ) {
        $this->data = $data;
        return $this->toObject();
    }
    /**
     * testing updateTimeStamps
     *
     * @param      $data
     * @param bool $insert
     */
    public function callUpdateTimeStamps( &$data, $insert=false ) {
        $this->data = & $data;
        $this->updateTimeStamps( $insert );
    }

    /**
     * testing toString
     * @param $data
     * @return array|mixed|string
     */
    public function callToString( $data ) {
        $this->data = & $data;
        return $this->toString();
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