<?php
namespace tests\ConstructTest;

use WScore\Models\DaoArray;

class TestDaoArray extends DaoArray
{
    /**
     * @var array
     */
    protected $timeStamps = array(
        'created_at' => [ 'created_at', 'Y-m-d H:i:s' ],
        'updated_at' => 'updated_at',
        'created_date' => [ 'creation_date', 'Y-m-d' ],
    );

    public $lastValue;
    
    // +---------------------+
    /**
     * hooks for test event.
     * @param null $value
     */
    protected function onTest( $value=null )
    {
        $this->lastValue = 'hook-tested:'.$value;
    }

    /**
     * fire 'test' event.
     * @param $value
     */
    public function testHooks( $value )
    {
        $this->hooks( 'test', $value );
    }

    // +---------------------+
    /**
     * for testing scope.
     */
    protected function scopeScopeTest()
    {
        $this->query->where( 'scope-test' );
    }

    // +---------------------+
    /**
     * hooks to keep the last (converted) value for update/insert.
     *
     * @param string $event
     * @param array  $value
     * @return array|mixed|null
     */
    protected function hooks( $event, $value=null )
    {
        $value = parent::hooks( $event, $value );
        switch( $event ) {
            case 'inserted':
            case 'updated':
                $this->lastValue = $value;
                break;
            default:
        }
        return $value;
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