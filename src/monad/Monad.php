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

namespace monad;


/**
 * A monad.
 */
class Monad {

    private $value;


    protected final function __construct( $value ) {
        $this->value = $this->preprocess( $value );
    }


    protected function preprocess( $value ) {
        return $value;
    }


    protected function val() {
        return $this->value;
    }


    /**
     * Bind a function to this monad.
     *
     * @param callable $function the function
     * @return Monad a monad with result of the function
     */
    public function continue( callable $function ): Monad {
        return static::create( $function( $this->val() ) );
    }


    /**
     * Bind a function that returns new monad to this monad.
     *
     * @param callable $function the function that returns monad
     * @return Monad a monad returned by the function
     */
    public function bindMonad( callable $function ): Monad {
        return $function( $this->val() );
    }


    /**
     * Extract the value of the monad.
     *
     * @return mixed the value of the monad
     */
    public function extract() {
        return $this->val();
    }


    public function __toString(): string {
        return (string)$this->extract();
    }


    /**
     * Create a monad.
     *
     * @param mixed $value a value
     * @return Monad a monad
     */
    public static function create( $value ): Monad {
        return new static( $value );
    }

}
