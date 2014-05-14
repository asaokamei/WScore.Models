<?php
namespace WScore\DbGateway;

use RuntimeException;

/**
 * Class Dao
 * @package WScore\Dao
 *
 */
class Dao
{
    /**
     * @var Dao[]
     */
    protected static $instances = array();

    /**
     * @param \WScore\DbGateway\DaoArray|\WScore\DbGateway\DaoEntity $dao
     * @param string| null $name
     */
    public static function _setDaoObject( $dao, $name=null )
    {
        if( !$name ) {
            $name = get_class($dao);
            if( strpos($name,'\\')!==false ) {
                $name = substr( $name, strrpos( $name, '\\' )+1 );
            }
        }
        static::$instances[$name] = $dao;
    }

    /**
     * @param $name
     * @param $args
     * @throws \RuntimeException
     * @return Dao
     */
    public static function __callStatic( $name, $args )
    {
        if( !array_key_exists( $name, static::$instances ) ) {
            throw new RuntimeException('no such dao: '.$name );
        }
        return static::$instances[$name];
    }

    /**
     *
     */
    public static function _cleanUp()
    {
        static::$instances = array();
    }

    // +----------------------------------------------------------------------+
}