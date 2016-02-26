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


/**
 * Map an array or traversable to another array.
 * 
 * @param callable $mapper a mapper to map each value;
 * value will be passed to the mapper as the first argument,
 * key will be passed as the second one
 * @param array|\Traversable $traversable the traversable to map
 * @param bool $preserve_keys preserve original keys in the mapped array
 * (optional, default is true)
 * @return array the mapped array
 */
function traversable_map( callable $mapper, $traversable, bool $preserve_keys = true ): array {
    $array_mapped = [];
    foreach( to_traversable( $traversable ) as $key => $value ) {
        $value_mapped = call_user_func( $mapper, $value, $key );
        if( $preserve_keys ) {
            $array_mapped[ $key ] = $value_mapped;
        } else {
            $array_mapped[] = $value_mapped;
        }
    }
    return $array_mapped;
}

const traversable_map = '\\convert\\traversable_map';
