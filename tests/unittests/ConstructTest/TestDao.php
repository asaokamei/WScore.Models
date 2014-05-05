<?php
namespace tests\ConstructTest;

use WScore\DbGateway\Dao;

class TestDao extends Dao
{
    protected $created_date = 'creation_date';
    
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