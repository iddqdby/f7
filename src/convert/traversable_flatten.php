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


/**
 * Flatten an array or a traversable.
 *
 * @param array|\Traversable $traversable the traversable to flatten
 * @param bool $preserve_keys preserve original keys of all arrays (default is false)
 * @return array flat array
 */
function traversable_flatten( $traversable, bool $preserve_keys = false ): array {
    $flat = [];
    if( $preserve_keys ) {
        
        foreach( to_traversable( $traversable ) as $key => $value ) {
            if( is_traversable( $value ) ) {
                $flat = array_merge( $flat, traversable_flatten( $value, true ) );
            } else {
                $flat[ $key ] = $value;
            }
        }
        
    } else {
        
        foreach( to_traversable( $traversable ) as $value ) {
            if( is_traversable( $value ) ) {
                foreach( traversable_flatten( $value, false ) as $subvalue ) {
                    $flat[] = $subvalue;
                }
            } else {
                $flat[] = $value;
            }
        }
        
    }
    return $flat;
}

const traversable_flatten = '\\convert\\traversable_flatten';
