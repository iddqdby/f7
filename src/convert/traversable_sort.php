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
 * Sort values of a traversable and return new array with values in sorted order.
 * 
 * @param callable|int $flags_or_comparator either one of SORT_* constants
 * or a comparator function
 * @param array|\Traversable $traversable a traversable to sort
 * @return array array with sorted values
 */
function traversable_sort( $flags_or_comparator, $traversable ): array {
    $array = to_array( $traversable );
    if( is_callable( $flags_or_comparator ) ) {
        usort( $array, $flags_or_comparator );
    } else {
        sort( $array, $flags_or_comparator );
    }
    return $array;
}

const traversable_sort = '\\convert\\traversable_sort';
