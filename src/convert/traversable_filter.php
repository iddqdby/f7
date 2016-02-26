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
 * Filter an array or a traversable.
 *
 * @param callable $predicate a predicate to test each value and key;
 * value will be passed to the predicate as the first argument,
 * key will be passed as the second one
 * @param array|\Traversable $traversable the traversable to filter
 * @param bool $preserve_keys preserve original keys in the filtered array
 * (optional, default is true)
 * @return array the filtered array
 */
function traversable_filter( callable $predicate, $traversable, bool $preserve_keys = true ): array {
    $array_filtered = [];
    foreach( to_traversable( $traversable ) as $key => $value ) {
        if( !call_user_func( $predicate, $value, $key ) ) {
            continue;
        }
        if( $preserve_keys ) {
            $array_filtered[ $key ] = $value;
        } else {
            $array_filtered[] = $value;
        }
    }
    return $array_filtered;
}

const traversable_filter = '\\convert\\traversable_filter';
