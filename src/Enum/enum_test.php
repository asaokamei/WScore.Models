<?php
namespace Sandbox\Enum;

require( __DIR__ . '/EnumInterface.php' );
require( __DIR__ . '/Enum.php' );
require( __DIR__ . '/Code.php' );

/**
 * Class Gender
 *
 * @package Sandbox\Enum
 */
class Gender extends Enum
{
    const MALE   = 'M';
    const FEMALE = 'F';
}

class Sex extends Code
{
    const MALE   = 'M';
    const FEMALE = 'F';
    
    protected $codes = array(
        Sex::MALE   => '男性',
        Sex::FEMALE => '女性'
    );
}

$active  = Sex::set( Sex::MALE );
$active2 = $active( Sex::MALE );

try {
    Sex::set( '3' );
} catch( \Exception $e ) {
    echo 'thrown exception:' . get_class( $e ) . PHP_EOL;
}

echo $active . PHP_EOL;
echo $active2 . PHP_EOL;

if( $active == $active2 ) {
    echo 'they are equal' . PHP_EOL;
} else {
    echo 'they are not equal' . PHP_EOL;
}
if( $active === $active2 )  {
    echo 'they are the same' . PHP_EOL;
} else {
    echo 'they are not the same' . PHP_EOL;
}

/*
 * Enumの問題点として。
 * ・インスタンスが異なるので「===」の比較でfalse
 * ・インジェクトできない。
 * 
 */