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
 * Catch and process exceprions of a function.
 *
 * @param callable $function the function
 * @param callable $exception_handler an exception handler; the exception will be passed
 * as the first argument, an array of arguments of the function will be passed as
 * the second argument; a result of the exception handler will be returned as a result of
 * the function
 * @param string $exception_class an exception class to catch (optional, default is "\Throwable")
 * @return callable the function whose exceptions will be handled by the exception handler
 */
function catch_ex( callable $function, callable $exception_handler, string $exception_class = \Throwable::class ): callable {
    return function ( ...$args ) use ( $function, $exception_handler, $exception_class ) {
        try {
            return call_user_func_array( $function, func_get_args() );
        } catch( \Throwable $ex ) {
            if( !$ex instanceof $exception_class ) {
                throw $ex;
            }
            return call_user_func( $exception_handler, $ex, $args );
        }
    };
}

const catch_ex = '\\func\\catch_ex';
