<?php
namespace tests\ConverterTest;

use DateTime;
use WScore\Models\Converter;

require_once( dirname( dirname( __DIR__ ) ) . '/autoload.php' );

class Converter_Test extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Converter
     */
    public $convert;

    function setup()
    {
        class_exists( 'WScore\Models\Converter' );
        $this->convert = new Converter();
    }

    function test0()
    {
        $this->assertEquals( 'WScore\Models\Converter', get_class($this->convert ) );
    }

    /**
     * @test
     */
    function toEntity_converts_input_array_to_ArrayObject()
    {
        $input = [ 'test' => 'tested' ];
        $entity = $this->convert->toEntity($input);
        $this->assertEquals( 'ArrayObject', get_class($entity ) );
        $this->assertSame( $input['test'], $entity['test'] );
    }

    /**
     * @test
     */
    function toArray_converts_DateTime_to_string()
    {
        $now = new \DateTime();
        $input = [ 'test' => $now ];
        $array = $this->convert->toArray($input);
        $this->assertEquals( true, is_string($array['test'] ) );
        $this->assertSame( $now->format('Y-m-d H:i:s'), $array['test'] );
    }

    /**
     * @test
     */
    function setting_datetime_to_column_converts_obj_to_string_in_toArray()
    {
        $now = new \DateTime();
        $fmt = 'Ymd-His';
        $input = [ 'test' => $now ];
        $this->convert->setDateTime( 'test', $fmt );
        $array = $this->convert->toArray($input);
        $this->assertEquals( true, is_string($array['test'] ) );
        $this->assertSame( $now->format($fmt), $array['test'] );
    }

    /**
     * @test
     */
    function setting_datetime_to_column_converts_string_to_object_in_toEntity()
    {
        $input = [ 'test' => '2014-05-07 12:34:56' ];
        $this->convert->setDateTime( 'test' );
        $entity = $this->convert->toEntity($input);
        $this->assertEquals( true, is_object($entity['test'] ) );
        /** @var DateTime $testDate */
        $testDate = $entity['test'];
        $this->assertSame( $input['test'], $testDate->format('Y-m-d H:i:s') );
    }
}
