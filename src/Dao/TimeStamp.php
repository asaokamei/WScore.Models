<?php
namespace WScore\Models\Dao;

use WScore\Models\Entity\Magic;

class TimeStamp
{
    const COLUMN = 0;
    const FORMAT = 1;

    public $date_time_format = 'Y-m-d H:i:s';
    
    /**
     * @var array
     */
    protected $time_stamps = array();
    
    /**
     * @param array $stamps
     */
    public function setTimeStamps( $stamps )
    {
        $this->time_stamps = $stamps;
    }
    
    /**
     * bad method! must rewrite!
     *
     * @return \DateTime
     */
    protected function getCurrentTime()
    {
        static $now;
        if( !$now ) $now = new \DateTime();
        return $now;
    }

    /**
     * @param array $data
     * @return array
     */
    public function onInsertingFilter( $data )
    {
        $data = $this->onUpdatingFilter( $data );
        $this->setTimeStamp( $data, 'created_at' );
        $this->setTimeStamp( $data, 'created_date' );
        $this->setTimeStamp( $data, 'created_time' );
        return $data;
    }

    /**
     * @param array $data
     * @return array
     */
    public function onUpdatingFilter( $data )
    {
        $this->setTimeStamp( $data, 'updated_at' );
        $this->setTimeStamp( $data, 'updated_date' );
        $this->setTimeStamp( $data, 'updated_time' );
        return $data;
    }

    /**
     * @param array $data
     * @param $info
     */
    protected function setTimeStamp( & $data, $info )
    {
        if( !isset( $this->time_stamps[$info] ) ) return;
        $info = $this->time_stamps[$info];
        if( is_array( $info ) ) {
            $column = $info[ self::COLUMN ];
            $format = $info[ self::FORMAT ];
        } else {
            $column = $info;
            $format = $this->date_time_format;
        }
        $now    = $this->getCurrentTime();
        Magic::set( $data, $column, $now->format( $format ) );
    }

    /**
     * @param $data
     * @return mixed
     */
    public function onSelectedFilter( $data )
    {
        $this->setDateTime( $data, 'created_at' );
        $this->setDateTime( $data, 'updated_at' );
        return $data;
    }

    /**
     * @param $data
     * @param $info
     */
    protected function setDateTime( & $data, $info ) 
    {
        if( !isset( $this->time_stamps[$info] ) ) return;
        $info = $this->time_stamps[$info];
        if( is_array( $info ) ) {
            $column = $info[ self::COLUMN ];
        } else {
            $column = $info;
        }
        if( $time = Magic::get( $data, $column ) ) {
            Magic::set( $data, $column, new \DateTime($time) );
        }
    }
}