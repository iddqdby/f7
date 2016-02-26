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

use function func\conditionally;
use function func\array_value_getter;
use function func\conjunction;
use function func\reverse;
use function func\curry;
use function func\method_caller;
use const meta\is_array_access;
use const func\property_getter;


/**
 * Chain monad.
 */
class Chain extends Monad {

    
    /**
     * {@inheritDoc}
     * 
     * Value will be mapped only if it is not NULL,
     * otherwise an empty Chain will be returned.
     * 
     * @see \monad\Monad::map($mapper)
     * @see \monad\Chain::emptyChain()
     */
    public function map( callable $mapper ): Chain {
        return null === $this->val()
                ? self::emptyChain()
                : parent::map( $mapper );
    }
    
    
    /**
     * Get key of an array or \ArrayAccess, if both are set.
     * 
     * @param int|string $key the key
     * @return Chain the value of the key wrapped into a Chain, or empty Chain
     */
    public function getKey( $key ): Chain {
        return $this->map( conditionally(
                conjunction(
                    is_array_access,
                    function ( $value ) use ( $key ) {
                        return isset( $value[ $key ] );
                    }
                ),
                array_value_getter( $key )
        ) );
    }
    
    
    /**
     * Get property of an object, if both are set.
     *
     * @param string $property the name of the property
     * @return Chain the value of the property wrapped into a Chain, or empty Chain
     */
    public function getProperty( string $property ): Chain {
        return $this->map( conditionally(
                curry( reverse( property_exists ), 2 )( $property ),
                property_getter( $property )
        ) );
    }
    
    
    /**
     * Call method of an object, if both are set.
     *
     * @param string $method the name of the method
     * @param mixed ...$args  optional arguments to pass to the method
     * @return Chain the result of the method wrapped into a Chain, or empty Chain
     */
    public function callMethod( string $method, ...$args ): Chain {
        return $this->callMethodArray( $method, $args );
    }
    
    
    /**
     * Call method of an object, if both are set.
     *
     * @param string $method the name of the method
     * @param array $args optional array of arguments to pass to the method
     * @return Chain the result of the method wrapped into a Chain, or empty Chain
     */
    public function callMethodArray( string $method, array $args = [] ): Chain {
        return $this->map( conditionally(
                curry( reverse( method_exists ), 2 )( $method ),
                method_caller( $method, $args )
        ) );
    }
    
    
    /**
     * Invoke a value if it is callable.
     * 
     * @param mixed ...$args optional arguments to pass to the optional callable
     * @return Chain the result of the invocation wrapped into a Chain, or empty Chain
     */
    public function invoke( ...$args ): Chain {
        return $this->invokeArray( $args );
    }
    
    
    /**
     * Invoke a value if it is callable.
     * 
     * @param array $args optional array of arguments to pass to the optional callable
     * @return Chain the result of the invocation wrapped into a Chain, or empty Chain
     */
    public function invokeArray( array $args = [] ): Chain {
        return $this->map( conditionally(
                is_callable,
                curry( reverse( call_user_func_array ), 2 )( $args )
        ) );
    }
    
    
    /**
     * Get optional result of the chain.
     * 
     * @return Optional optional result of the chain
     */
    public function result(): Optional {
        return $this->flatMap( optional );
    }


    /**
     * Get empty Chain (the Chain with NULL value).
     * 
     * @return Chain the empty Chain
     */
    public static final function emptyChain(): Chain {
        static $empty_chain = null;
        if( !$empty_chain ) {
            $empty_chain = new self( null );
        }
        return $empty_chain;
    }

}
