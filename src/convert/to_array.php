<?php

/*
 * The MIT License
 *
 * Copyright 2016 Sergey Protasevich.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

namespace convert;

use function meta\is_traversable;
use const func\pass_through;


/**
 * Convert a traversable or other value into an array or leave it as is if it already is an array.
 *
 * Additional argument can be set to true to force conversion of empty values
 * into empty arrays. By default any not-array and not-traversable value (except of null) will
 * just be wrapped into an array with single element.
 *
 * @param mixed $var the value
 * @param bool $empty_value_to_empty_array force conversion of empty values
 * into empty arrays (optional, default is false)
 * @return array the value as is if it is an array, or the value
 * converted to an array
 */
function to_array( $var, bool $empty_value_to_empty_array = false ): array {
    
    if( $empty_value_to_empty_array && empty( $var ) ) {
        return [];
    }
    
    if( is_array( $var ) ) {
        return $var;
    }
    
    if( is_traversable( $var ) ) {
        return traversable_map( pass_through, $var );
    }
    
    return (array)$var;
}

const to_array = '\\convert\\to_array';
