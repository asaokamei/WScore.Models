<?php
/**
 * Created by PhpStorm.
 * User: asao
 * Date: 2013/11/26
 * Time: 23:44
 */
namespace WScore\Models\Enum;

interface EnumInterface
{
    /**
     * Returns all possible values and strings as an array
     *
     * @return array Constant name in key, constant value in value
     */
    public static function getChoices();

    /**
     * Returns all possible values as an array.
     *
     * @return mixed
     */
    public static function getValues();

    /**
     * @param $value
     * @return bool
     */
    public static function exists( $value );

    /**
     * @param $value
     * @return string
     */
    public static function choose( $value );

    /**
     * @return string
     */
    public function __toString();

    /**
     * @return string
     */
    public function get();

    /**
     * @return string
     */
    public function show();

    /**
     * @param $value
     * @return bool
     */
    public function is( $value );
}