<?php
namespace tests\EnumTest;

use WScore\Models\Enum\AbstractEnum;

require_once( dirname( dirname( __DIR__ ) ) . '/autoload.php' );

/**
 * Class Gender
 * @package tests\EnumTest
 *
 * @method bool isMale
 * @method bool isFemale
 * @method bool isWhat
 * @method bool bad_method
 */
class Gender extends AbstractEnum
{
    const MALE   = 'M',
          FEMALE = 'F';
    protected static $choices = [
        self::MALE => 'Male',
        self::FEMALE => 'Female'
    ];
}

class Enum_Test extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Gender
     */
    var $enum;

    function setup()
    {
        $this->enum = new Gender(Gender::FEMALE);
    }

    function test0()
    {
        $this->assertEquals( 'tests\EnumTest\Gender', get_class($this->enum) );
    }

    /**
     * @test
     */
    function enum_gets_value_of_female()
    {
        $this->assertEquals( Gender::FEMALE, $this->enum->get() );
        $this->assertEquals( Gender::FEMALE, $this->enum );
        $this->assertEquals( Gender::choose(Gender::FEMALE), $this->enum->show() );
    }

    /**
     * @test
     */
    function enum_is_check_the_value()
    {
        $this->assertTrue( $this->enum->is( Gender::FEMALE ) );
        $this->assertTrue( $this->enum->isFemale() );
        $this->assertFalse( $this->enum->is( Gender::MALE ) );
        $this->assertFalse( $this->enum->isMale() );
        $this->assertFalse( $this->enum->isWhat() );
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    function enum_setting_bad_value_throws_exception()
    {
        new Gender( 'X' );
    }

    /**
     * @test
     */
    function enum_static_methods_returns_choices()
    {
        $this->assertEquals( ['M','F'], Gender::getValues() );
        $this->assertEquals( ['M'=>'Male','F'=>'Female'], Gender::getChoices() );
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    function enum_throws_exception_for_bad_method()
    {
        $this->enum->bad_method();
    }
}
