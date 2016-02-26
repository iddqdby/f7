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
 * Execute an action conditionally.
 *
 * Function will execute the action, if a predicate returns true, otherwise it will
 * optionally execute an "else" action.
 *
 * @param callable $predicate the predicate
 * @param callable $action the action
 * @param callable $else_action the action to perform if condition returns false (optional)
 * @return callable function that returns a result of called action depending
 * on the result of the predicate
 */
function conditionally( callable $predicate, callable $action, callable $else_action = null ): callable {
    return function ( ...$args ) use ( $predicate, $action, $else_action ) {
        if( call_user_func_array( $predicate, $args ) ) {
            return call_user_func_array( $action, $args );
        } elseif( $else_action ) {
            return call_user_func_array( $else_action, $args );
        }
        return null;
    };
}

const conditionally = '\\func\\conditionally';
