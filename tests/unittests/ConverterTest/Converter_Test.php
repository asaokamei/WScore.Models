<?php
namespace tests\ConverterTest;

use DateTime;
use WScore\Models\Dao\Converter;

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
        $this->assertEquals( 'WScore\Models\Dao\Converter', get_class($this->convert ) );
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
}
