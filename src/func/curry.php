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

namespace func;


/**
 * Curry a function.
 *
 * @param callable $function curried function
 * @param int|bool $args_num number of arguments to use,
 * true to use all arguments, false to use all required arguments
 * @return callable curried function
 * @throws \InvalidArgumentException if number of arguments
 */
function curry( callable $function, $args_num ): callable {

    if( is_bool( $args_num ) ) {
        $args_num = \meta\args_num( $function, $args_num );
    } else {
        $args_num = intval( $args_num );
    }
    if( $args_num < 1 ) {
        throw new \InvalidArgumentException( 'Number of arguments must be greater than zero' );
    }

    $collector = function () use ( $function, $args_num, &$collector ) {

        static $args = [];
        $args[] = @func_get_arg( 0 );

        if( count( $args ) === $args_num ) {
            $result = call_user_func_array( $function, $args );
            $args = [];
            return $result;
        }
        return $collector;
    };
    return $collector;
}

const curry = '\\func\\curry';
