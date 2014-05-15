<?php
namespace WScore\Models\Dao;

use WScore\Models\Entity\Magic;

class TimeStamp
{
    const COLUMN = 0;
    const FORMAT = 1;
    protected $created_at   = '';
    protected $created_date = '';
    protected $created_time = '';
    protected $updated_at   = '';
    protected $updated_date = '';
    protected $updated_time = '';

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
     * @param string $column
     * @param string $format
     */
    public function setUpdatedAt( $column, $format )
    {
        $this->updated_at = [ self::COLUMN => $column, self::FORMAT => $format ];
    }

    /**
     * @param string $column
     * @param string $format
     */
    public function setUpdatedDate( $column, $format )
    {
        $this->updated_date = [ self::COLUMN => $column, self::FORMAT => $format ];
    }

    /**
     * @param string $column
     * @param string $format
     */
    public function setUpdatedTime( $column, $format )
    {
        $this->updated_time = [ self::COLUMN => $column, self::FORMAT => $format ];
    }

    /**
     * @param string $column
     * @param string $format
     */
    public function setCreatedAt( $column, $format )
    {
        $this->created_at = [ self::COLUMN => $column, self::FORMAT => $format ];
    }

    /**
     * @param string $column
     * @param string $format
     */
    public function setCreatedDate( $column, $format )
    {
        $this->created_date = [ self::COLUMN => $column, self::FORMAT => $format ];
    }

    /**
     * @param string $column
     * @param string $format
     */
    public function setCreatedTime( $column, $format )
    {
        $this->created_time = [ self::COLUMN => $column, self::FORMAT => $format ];
    }

    /**
     * @param array $data
     * @return array
     */
    public function onInsertingFilter( $data )
    {
        $data = $this->onUpdatingFilter( $data );
        $this->setTimeStamp( $data, $this->created_at );
        $this->setTimeStamp( $data, $this->created_date );
        $this->setTimeStamp( $data, $this->created_time );
        return $data;
    }

    /**
     * @param array $data
     * @return array
     */
    public function onUpdatingFilter( $data )
    {
        $this->setTimeStamp( $data, $this->updated_at );
        $this->setTimeStamp( $data, $this->updated_date );
        $this->setTimeStamp( $data, $this->updated_time );
        return $data;
    }

    /**
     * @param array $data
     * @param $info
     */
    protected function setTimeStamp( & $data, $info )
    {
        if( !$info ) return;
        $column = $info[ self::COLUMN ];
        $format = $info[ self::FORMAT ];
        $now    = $this->getCurrentTime();
        Magic::set( $data, $column, $now->format( $format ) );
    }

    /**
     * @param $data
     * @return mixed
     */
    public function onSelectedFilter( $data )
    {
        $this->setDateTime( $data, $this->created_at );
        $this->setDateTime( $data, $this->updated_at );
        return $data;
    }

    /**
     * @param $data
     * @param $info
     */
    protected function setDateTime( & $data, $info ) 
    {
        if( !$info ) return;
        $column = $info[self::COLUMN];
        if( $time = Magic::get( $data, $column ) ) {
            Magic::set( $data, $column, new \DateTime($time) );
        }
    }
}