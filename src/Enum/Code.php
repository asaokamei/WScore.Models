<?php
namespace Sandbox\Enum;

class Code implements EnumInterface
{
    /**
     * list of available enum values and label.
     * $codes = array( [ code => label, ... ] );
     * 
     * @var array
     */
    protected $codes = array();

    /**
     * @var string
     */
    protected $value;

    /**
     * @var array
     */
    private static $instances = array();
    
    /**
     * @var array
     */
    protected static $_codes = array();
    
    /**
     */
    public function __construct()
    {
        if( !static::$_codes ) {
            static::$_codes = $this->codes;
        }
    }

    /**
     * 
     */
    public function resetCodes()
    {
        $this->codes = static::$_codes;
    }
    
    /**
     * @param $value
     * @throws \InvalidArgumentException
     * @return EnumInterface
     */
    public static function set( $value )
    {
        $class = get_called_class();
        $name  = (string) $value;
        
        if( !isset( self::$instances[$class][$name] ) ) {            
            $code = new $class();
            if( !isset( $code->codes[$value] ) ) {
                throw new \InvalidArgumentException();
            }
            $code->value = $value;
            self::$instances[$class][$name] = $code;
        }
        return self::$instances[$class][$name];
    }

    /**
     * @return array
     */
    public static function toArray()
    {
        return static::$_codes;
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
     * @return string
     */
    public function toLabel()
    {
        return $this->codes[ $this->value ];
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
}