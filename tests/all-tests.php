<?php

class DbGateway_tests
{
    public static function suite()
    {
        $suite = new \PHPUnit_Framework_TestSuite( 'DbGateway Test Suites' );
        $suite->addTestFile( __DIR__.'/unittests/ConverterTest/Converter_Test.php' );
        $suite->addTestFile( __DIR__.'/unittests/EnumTest/Enum_Test.php' );
        $suite->addTestFile( __DIR__.'/unittests/ConstructTest/Dao_UnitTest.php' );
        $suite->addTestFile( __DIR__.'/unittests/ConstructTest/DaoQuery_Test.php' );
        $suite->addTestFile( __DIR__.'/FunctionalTests/DaoFunctionTest/Dao_BasicTest.php' );
        return $suite;
    }
}