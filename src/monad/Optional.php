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

use function func\negation;
use function func\conditionally;
use const func\pass_through;


/**
 * Optional (aka. "Maybe") monad.
 */
class Optional extends Monad {


    /* Monad::bind() shorthands */


    /**
     * Map a value of the monad if it is present.
     *
     * See <code>isPresent()</code>.
     *
     * @param callable $function the mapper
     * @return Optional a new Optional if the value was present, or this otherwise
     */
    public function ifPresent( callable $function ): Optional {
        return $this->bind( conditionally(
                negation( is_null ),
                $function
        ) );
    }


    /**
     * Filter a value of the monad by a predicate.
     *
     * If the value matches the predicate, this monad will be returned,
     * otherwise monad with NULL value will be returned.
     *
     * @param callable $predicate the predicate to test the value
     * @return Optional this monad if the value matches the predicate,
     * otherwise a monad with NULL value
     */
    public function filter( callable $predicate ): Optional {
        return $this->bind( conditionally(
                $predicate,
                pass_through
        ) );
    }


    /* Monad::bindMonad() shorthands */


    /**
     * Wrap the value of the Optional into a Stream.
     *
     * @return Stream the Stream
     * @see \monad\Stream
     */
    public function stream(): Stream {
        return $this->bindMonad( stream );
    }


    /**
     * Wrap the value of the Optional into a Chain.
     *
     * @return Chain the Chain
     * @see \monad\Chain
     */
    public function chain(): Chain {
        return $this->bindMonad( chain );
    }


    /* Monad::extract() shorthands */


    /**
     * Is a value of the monad present (not equal to NULL).
     *
     * @return bool true if the value of the monad is not equal to NULL
     */
    public function isPresent(): bool {
        return null !== $this->extract();
    }


    /**
     * Get a value of the monad if it is present, or get provided default value otherwise.
     *
     * See <code>isPresent()</code>.
     *
     * @param mixed $default default falue to return if the value of the monad
     * is not present
     * @return mixed the value of the monad, or default one if the value is not present
     */
    public function orElse( $default = null ) {
        return $this->isPresent()
                ? $this->extract()
                : $default;
    }


    /**
     * Get a value of the monad if it is present, or call the supplier and
     * get its result otherwise.
     *
     * See <code>isPresent()</code>.
     *
     * @param callable $supplier supplier to produce default value to return
     * if the value of the monad is not present
     * @return mixed the value of the monad, or a result of the supplier
     * if the value is not present
     */
    public function orElseGet( callable $supplier ) {
        return $this->isPresent()
                ? $this->extract()
                : $supplier();
    }


    /**
     * Get a value of the monad if it is present, or throw an exception
     * returned by an exception supplier otherwise.
     *
     * See <code>isPresent()</code>.
     *
     * @param callable $supplier supplier to produce an exception to throw
     * if the value of the monad is not present
     * @return mixed the value of the monad
     * @throws \Throwable if the value is not present
     */
    public function orElseThrow( callable $exception_supplier ) {
        if( $this->isPresent() ) {
            return $this->extract();
        }
        throw $exception_supplier();
    }


    /* Factory methods */


    /**
     * Get empty Optional (the Optional with NULL value).
     *
     * @return Optional the empty Optional
     */
    public static final function emptyOptional(): Optional {
        static $empty_optional = null;
        if( !$empty_optional ) {
            $empty_optional = new self( null );
        }
        return $empty_optional;
    }


    /**
     * {@inheritDoc}
     */
    public static function create( $value ): Chain {
        return null === $value ? self::emptyOptional() : new self( $value );
    }

}
