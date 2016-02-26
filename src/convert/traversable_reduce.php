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
 * Iteratively reduce an array or a traversable to a single value
 * using a callback function.
 * 
 * Function applies iteratively the callback function to elements and keys
 * of the array or the traversable, so as to reduce it to a single value.
 * 
 * @param callable $callback the callback to apply to each element and key;
 * current intermediate result will be passed as the first argument
 * (in the case of the first iteration it instead holds the value of
 * <code>$initial</code>), current value will be passed as the second one,
 * current key will be passed as the third one; callback must return
 * new intermediate result
 * @param mixed $initial initial value
 * @param array|\Traversable $traversable the input traversable
 * @return mixed the resulting value
 */
function traversable_reduce( callable $callback, $initial, $traversable ) {
    $result = $initial;
    foreach( to_traversable( $traversable ) as $key => $value ) {
        $result = call_user_func( $callback, $result, $value, $key );
    }
    return $result;
}

const traversable_reduce = '\\convert\\traversable_reduce';
