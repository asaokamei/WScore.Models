<?php
/**
 * Created by PhpStorm.
 * User: asao
 * Date: 2013/11/26
 * Time: 23:44
 */
namespace Sandbox\Enum;

interface EnumInterface
{
    /**
     * Returns all possible values as an array
     *
     * @return array Constant name in key, constant value in value
     */
    public static function toArray();

    /**
     * creates an instance of a $value. 
     * 
     * @param $value
     * @throws \InvalidArgumentException
     * @return EnumInterface
     */
    public static function set( $value );

    /**
     * creates another instance of a $value. 
     * 
     * @param $value
     * @return EnumInterface
     */
    public function __invoke( $value );

    /**
     * @return mixed
     */
    public function toRaw();

    /**
     * @return string
     */
    public function __toString();

}