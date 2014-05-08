<?php
namespace Sandbox\Enum;

abstract class Enum implements EnumInterface
{
    /**
     * Enum value
     * @var mixed
     */
    protected $value;

    /**
     * Store existing constants in a static cache per object.
     * @var array
     */
    private static $constantsCache = array();

    /**
     * Store instances of enum objects. 
     * 
     * @var Enum[]
     */
    private static $instances = array();
    
    /**
     */
    private function __construct()
    {
    }

    /**
     * @param $value
     * @return EnumInterface
     */
    public function __invoke( $value )
    {
        return static::set( $value );
    }

    /**
     * @param $value
     * @throws \InvalidArgumentException
     * @return EnumInterface
     */
    public static function set( $value )
    {
        $possibleValues = self::toArray();
        if (! in_array($value, $possibleValues)) {
            throw new \InvalidArgumentException();
        }
        $class = get_called_class();
        $name  = (string) $value;
        if( !isset( self::$instances[$class][$name] ) ) {
            $enum = new $class( $value );
            $enum->value = $value;
            self::$instances[$class][$name] = $enum;
        }
        return self::$instances[$class][$name];
    }
    
    /**
     * @return mixed
     */
    public function toRaw()
    {
        return $this->value;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->value;
    }

    /**
     * Returns all possible values as an array
     * @return array Constant name in key, constant value in value
     */
    public static function toArray()
    {
        $calledClass = get_called_class();
        if(!array_key_exists($calledClass, self::$constantsCache)) {
            $reflection = new \ReflectionClass($calledClass);
            self::$constantsCache[$calledClass] = $reflection->getConstants();
        }
        return self::$constantsCache[$calledClass];
    }
}