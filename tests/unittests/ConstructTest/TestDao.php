<?php
namespace tests\ConstructTest;

use WScore\DbGateway\Dao;

class TestDao extends Dao
{
    protected $created_date = 'creation_date';

    public $lastValue;

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
     * hooks to keep the last (converted) value for update/insert.
     *
     * @param string $event
     * @param array $value
     */
    protected function hooks( $event, $value=array() )
    {
        switch( $event ) {
            case 'inserted':
            case 'updated':
                $this->lastValue = $value;
                break;
            default:
        }
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