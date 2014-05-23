<?php

class DbGateway_tests
{
    public static function suite()
    {
        $suite = new \PHPUnit_Framework_TestSuite( 'DbGateway Test Suites' );
        $suite->addTestFile( __DIR__.'/unitTests/ConverterTest/Converter_Test.php' );
        $suite->addTestFile( __DIR__.'/unitTests/EnumTest/Enum_Test.php' );
        $suite->addTestFile( __DIR__.'/unitTests/DaoTest/Dao_BasicTest.php' );
        $suite->addTestFile( __DIR__.'/unitTests/DaoArrayTest/DaoArray_UnitTest.php' );
        $suite->addTestFile( __DIR__.'/FunctionalTests/DaoFunctionTest/DaoEntity_BasicTest.php' );
        $suite->addTestFile( __DIR__.'/relationTests/Tests/Author_Test.php' );
        $suite->addTestFile( __DIR__.'/relationTests/Tests/Load_Test.php' );
        return $suite;
    }
}