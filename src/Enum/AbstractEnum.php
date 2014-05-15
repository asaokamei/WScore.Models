<?php
namespace WScore\Models\Enum;

/**
 * Class Enum
 * @package Sandbox\Enum
 *
 * most likely, this class is from the repository:
 * https://github.com/myclabs/php-enum
 *
 *
 */
abstract class AbstractEnum implements EnumInterface
{
    /**
     * Enum value
     *
     * @var mixed
     */
    protected $value;

    /**
     * values that can be selected.
     * [ value => string name, ... ]
     *
     * @var array
     */
    protected static $choices = array();

    // +----------------------------------------------------------------------+
    //  construction
    // +----------------------------------------------------------------------+
    /**
     * @param string $value
     * @throws \InvalidArgumentException
     */
    public function __construct( $value )
    {
        if( !$this->exists($value) ) {
            throw new \InvalidArgumentException( "no such value: ".$value );
        }
        $this->value = $value;
    }

    /**
     * Returns all possible values and strings as an array
     *
     * @return array Constant name in key, constant value in value
     */
    public static function getChoices()
    {
        return static::$choices;
    }

    /**
     * Returns all possible values as an array.
     *
     * @return mixed
     */
    public static function getValues()
    {
        return array_keys( static::$choices );
    }

    /**
     * @param $value
     * @return bool
     */
    public static function exists( $value )
    {
        return isset( static::$choices[$value] );
    }

    /**
     * @param $value
     * @return bool
     */
    public static function choose( $value )
    {
        return isset( static::$choices[$value] ) ? static::$choices[$value] : null;
    }

    // +----------------------------------------------------------------------+
    //  object method
    // +----------------------------------------------------------------------+
    /**
     * @return string
     */
    public function __toString()
    {
        return $this->get();
    }

    /**
     * @param string $method
     * @param array $args
     * @return bool|mixed
     * @throws \InvalidArgumentException
     */
    public function __call( $method, $args )
    {
        if( substr( $method, 0, 2 ) == 'is' ) {
            $const = strtoupper( substr( $method, 2 ) );
            if( defined( "static::{$const}" ) ) {
                return $this->is( constant( "static::{$const}" ) );
            }
            return false;
        }
        throw new \InvalidArgumentException( "no such method: ".$method );
    }

    /**
     * @return string
     */
    public function get()
    {
        return (string) $this->value;
    }

    /**
     * @return string
     */
    public function show()
    {
        return array_key_exists( $this->value, static::$choices ) ?
            static::$choices[$this->value] : null;
    }

    /**
     * @param $value
     * @return bool
     */
    public function is( $value )
    {
        return $value === $this->value;
    }
    // +----------------------------------------------------------------------+
}