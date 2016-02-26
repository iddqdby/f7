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


    protected function preprocess( $value ) {}


    protected function val() {
        return $this->value;
    }


    /**
     * Map a value of a monad with provided mapper.
     *
     * @param callable $mapper the mapper
     * @return Monad a monad with mapped value
     */
    public function map( callable $mapper ): Monad {
        return static::create( $mapper( $this->val() ) );
    }


    /**
     * Map a value of a monad to another monad with provided mapper.
     *
     * @param callable $mapper the mapper that returns monad
     * @return Monad a monad returned by mapper
     */
    public function flatMap( callable $mapper ): Monad {
        return $mapper( $this->val() );
    }


    /**
     * Get value of the monad.
     *
     * @return mixed the value of the monad
     */
    public function getValue() {
        return $this->val();
    }


    public function __toString(): string {
        return (string)$this->val();
    }


    public final function __invoke( callable $mapper ): Monad {
        return $this->map( $mapper );
    }


    /**
     * Create a monad.
     *
     * @param mixed $value a value
     * @return Monad a monad
     */
    public static final function create( $value ): Monad {
        return new static( $value );
    }

}
