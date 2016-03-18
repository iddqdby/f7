<?php

use function convert\to_array_like;
use function convert\to_array;
use function convert\to_traversable;


class convert extends PHPUnit_Framework_TestCase {


    /**
     * @covers \convert\to_array_like
     * @covers \convert\to_array
     * @covers \convert\to_traversable
     * @dataProvider provider_args
     */
    public function test_convert( $var, bool $empty_value_to_empty_array,
            $expected_to_array_like, $expected_to_array, $expected_to_traversable ) {

        $actual = to_array_like( $var, $empty_value_to_empty_array );
        $this->assertEquals( $expected_to_array_like, $actual );

        $actual = to_array( $var, $empty_value_to_empty_array );
        $this->assertEquals( $expected_to_array, $actual );

        $actual = to_traversable( $var );
        $this->assertEquals( $expected_to_traversable, $actual );
    }


    public function provider_args() {

        $resource = fopen( __FILE__, 'r' );
        $object_empty = new stdClass();
        $object_not_empty = new class {
            public $foo = 0;
            public $bar = 1;
            public $baz = 2;
        };
        $traversable_empty = new ArrayObject();
        $traversable_not_empty_0 = new ArrayObject([ 1, 2, 3 ]);
        $traversable_not_empty_1 = new ArrayObject([ 'foo' => 1, 'bar' => 2, 'baz' => 3 ]);

        return [
            'array_0' => [ [], false,
                            [],
                            [],
                            [],
            ],
            'array_1' => [ [], true,
                            [],
                            [],
                            [],
            ],
            'array_2' => [ [ 1, 2, 3 ], false,
                            [ 1, 2, 3 ],
                            [ 1, 2, 3 ],
                            [ 1, 2, 3 ],
            ],
            'array_3' => [ [ 1, 2, 3 ], true,
                            [ 1, 2, 3 ],
                            [ 1, 2, 3 ],
                            [ 1, 2, 3 ],
            ],
            'array_4' => [ [ 'foo' => 1, 'bar' => 2, 'baz' => 3 ], false,
                            [ 'foo' => 1, 'bar' => 2, 'baz' => 3 ],
                            [ 'foo' => 1, 'bar' => 2, 'baz' => 3 ],
                            [ 'foo' => 1, 'bar' => 2, 'baz' => 3 ],
            ],
            'array_5' => [ [ 'foo' => 1, 'bar' => 2, 'baz' => 3 ], true,
                            [ 'foo' => 1, 'bar' => 2, 'baz' => 3 ],
                            [ 'foo' => 1, 'bar' => 2, 'baz' => 3 ],
                            [ 'foo' => 1, 'bar' => 2, 'baz' => 3 ],
            ],
            'int_0' => [ 0, false,
                            [ 0 ],
                            [ 0 ],
                            [ 0 ],
            ],
            'int_1' => [ 0, true,
                            [],
                            [],
                            [ 0 ],
            ],
            'int_2' => [ 1, false,
                            [ 1 ],
                            [ 1 ],
                            [ 1 ],
            ],
            'int_3' => [ 1, true,
                            [ 1 ],
                            [ 1 ],
                            [ 1 ],
            ],
            'int_4' => [ PHP_INT_MIN, false,
                            [ PHP_INT_MIN ],
                            [ PHP_INT_MIN ],
                            [ PHP_INT_MIN ],
            ],
            'int_5' => [ PHP_INT_MAX, true,
                            [ PHP_INT_MAX ],
                            [ PHP_INT_MAX ],
                            [ PHP_INT_MAX ],
            ],
            'float_0' => [ 0., false,
                            [ 0. ],
                            [ 0. ],
                            [ 0. ],
            ],
            'float_1' => [ 0., true,
                            [],
                            [],
                            [ 0. ],
            ],
            'float_2' => [ 3.14, false,
                            [ 3.14 ],
                            [ 3.14 ],
                            [ 3.14 ],
            ],
            'float_3' => [ 3.14, true,
                            [ 3.14 ],
                            [ 3.14 ],
                            [ 3.14 ],
            ],
            'float_4' => [ INF, false,
                            [ INF ],
                            [ INF ],
                            [ INF ],
            ],
            'float_5' => [ INF, true,
                            [ INF ],
                            [ INF ],
                            [ INF ],
            ],
            'float_6' => [ -INF, false,
                            [ -INF ],
                            [ -INF ],
                            [ -INF ],
            ],
            'float_7' => [ -INF, true,
                            [ -INF ],
                            [ -INF ],
                            [ -INF ],
            ],
            'bool_0' => [ false, false,
                            [ false ],
                            [ false ],
                            [ false ],
            ],
            'bool_1' => [ false, true,
                            [],
                            [],
                            [ false ],
            ],
            'bool_2' => [ true, false,
                            [ true ],
                            [ true ],
                            [ true ],
            ],
            'bool_3' => [ true, true,
                            [ true ],
                            [ true ],
                            [ true ],
            ],
            'string_0' => [ '', false,
                            [ '' ],
                            [ '' ],
                            [ '' ],
            ],
            'string_1' => [ '', true,
                            [],
                            [],
                            [ '' ],
            ],
            'string_2' => [ 'abc', false,
                            [ 'abc' ],
                            [ 'abc' ],
                            [ 'abc' ],
            ],
            'string_3' => [ 'abc', true,
                            [ 'abc' ],
                            [ 'abc' ],
                            [ 'abc' ],
            ],
            'null_0' => [ null, false,
                            [],
                            [],
                            [],
            ],
            'null_1' => [ null, true,
                            [],
                            [],
                            [],
            ],
            'resource_0' => [ $resource, false,
                            [ $resource ],
                            [ $resource ],
                            [ $resource ],
            ],
            'resource_1' => [ $resource, true,
                            [ $resource ],
                            [ $resource ],
                            [ $resource ],
            ],
            'object_0' => [ $object_empty, false,
                            [],
                            [],
                            [],
            ],
            'object_1' => [ $object_empty, true,
                            [],
                            [],
                            [],
            ],
            'object_2' => [ $object_not_empty, false,
                            [ 'foo' => 0, 'bar' => 1, 'baz' => 2 ],
                            [ 'foo' => 0, 'bar' => 1, 'baz' => 2 ],
                            [ 'foo' => 0, 'bar' => 1, 'baz' => 2 ],
            ],
            'object_3' => [ $object_not_empty, true,
                            [ 'foo' => 0, 'bar' => 1, 'baz' => 2 ],
                            [ 'foo' => 0, 'bar' => 1, 'baz' => 2 ],
                            [ 'foo' => 0, 'bar' => 1, 'baz' => 2 ],
            ],
            'traversable_0' => [ $traversable_empty, false,
                            $traversable_empty,
                            [],
                            $traversable_empty,
            ],
            'traversable_1' => [ $traversable_empty, true,
                            $traversable_empty,
                            [],
                            $traversable_empty,
            ],
            'traversable_2' => [ $traversable_not_empty_0, false,
                            $traversable_not_empty_0,
                            [ 1, 2, 3 ],
                            $traversable_not_empty_0,
            ],
            'traversable_3' => [ $traversable_not_empty_0, true,
                            $traversable_not_empty_0,
                            [ 1, 2, 3 ],
                            $traversable_not_empty_0,
            ],
            'traversable_4' => [ $traversable_not_empty_1, false,
                            $traversable_not_empty_1,
                            [ 'foo' => 1, 'bar' => 2, 'baz' => 3 ],
                            $traversable_not_empty_1,
            ],
            'traversable_5' => [ $traversable_not_empty_1, true,
                            $traversable_not_empty_1,
                            [ 'foo' => 1, 'bar' => 2, 'baz' => 3 ],
                            $traversable_not_empty_1,
            ],
        ];
    }

}
