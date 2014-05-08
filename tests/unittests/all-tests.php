<?php

class DbGateway_tests
{
    public static function suite()
    {
        $suite = new \PHPUnit_Framework_TestSuite( 'DbGateway Test Suites' );
        $suite->addTestFile( __DIR__.'/ConstructTest/Dao_UnitTest.php' );
        $suite->addTestFile( __DIR__.'/ConverterTest/Converter_Test.php' );
        $suite->addTestFile( __DIR__.'/EnumTest/Enum_Test.php' );
        return $suite;
    }
}