<?php
namespace WScore\Models;

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
     * @param \WScore\Models\DaoArray|\WScore\Models\DaoEntity $dao
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
     * @param string|null $name
     * @return Dao
     * @throws \RuntimeException
     */
    public static function dao( $name=null )
    {
        if( !$name ) $name = get_called_class();
        if( array_key_exists( $name, static::$instances ) ) {
            return static::$instances[$name];
        }
        throw new RuntimeException('no such dao: '.$name );
    }
    
    /**
     * @param string $name
     * @param array $args
     * @throws \RuntimeException
     * @return Dao
     */
    public static function __callStatic( $name, $args )
    {
        if( array_key_exists( $name, static::$instances ) ) {
            return static::dao($name);
        }
        $dao = static::dao();
        if( method_exists( $dao, $name ) ) {
            return call_user_func_array( [$dao, $name], $args );
        }
        throw new RuntimeException('no such dao method: '.$name );
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