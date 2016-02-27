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

use function func\curry;
use function func\sequence;
use function func\reverse;
use function func\conditionally;
use function func\is_empty;
use function func\negation;
use function func\array_value_getter;
use function convert\to_array;
use function convert\traversable_map;
use function convert\traversable_flatten;
use function convert\traversable_filter;
use function convert\traversable_reduce;
use function convert\traversable_merge;
use function convert\traversable_walk;
use function convert\traversable_randomize;
use function convert\traversable_sort;
use function meta\all_match;
use function meta\any_match;
use function meta\none_match;
use function meta\statistics;
use function monad\optional;
use const func\curry;
use const func\sequence;
use const func\reverse;
use const func\conditionally;
use const func\is_empty;
use const func\negation;
use const func\array_value_getter;
use const convert\to_array;
use const convert\traversable_map;
use const convert\traversable_flatten;
use const convert\traversable_filter;
use const convert\traversable_reduce;
use const convert\traversable_merge;
use const convert\traversable_walk;
use const convert\traversable_randomize;
use const convert\traversable_sort;
use const meta\all_match;
use const meta\any_match;
use const meta\none_match;
use const meta\statistics;
use const monad\optional;
use meta\Statistics;
use Countable;
use InvalidArgumentException;
use function func\set_args;


/**
 * Stream monad.
 */
class Stream extends Monad implements Countable {

    private $close_handlers = [];


    protected function preprocess( $value ) {
        return to_array( $value );
    }


    /**
     * {@inheritDoc}
     *
     * @see \monad\Monad::bind($function)
     */
    public function bind( callable $function ): Stream {
        $stream = parent::bind( $function );
        $stream->close_handlers = $this->close_handlers;
        return $stream;
    }


    /**
     * {@inheritDoc}
     *
     * This is a closing operation (it calls <code>$this->close()</code> internally).
     *
     * @see \monad\Monad::bindMonad($function)
     * @see \monad\Stream::close()
     */
    public function bindMonad( callable $function ): Monad {
        $monad = parent::bindMonad( $function );
        $this->close();
        return $monad;
    }


    /**
     * Add close handler or handlers.
     *
     * Current value of the stream will be passed to each handler,
     * result of a handler will be ignored.
     *
     * @param callable|callable[] ...$handlers a handler, handlers, or array of handlers
     * @throws InvalidArgumentException
     * @return Stream this stream
     */
    public function onClose( ...$handlers ): Stream {
        foreach( traversable_flatten( $handlers ) as $handler ) {
            if( !is_callable( $handler ) ) {
                throw new InvalidArgumentException( 'Handler must be callable' );
            }
            $this->close_handlers[] = $handler;
        }
        return $this;
    }


    /* Monad::bind() shorthands */


    /**
     * Map each element of the stream.
     *
     * @param callable $mapper a mapper
     * @return Stream a stream
     * @see \convert\traversable_map
     */
    public function mapEach( callable $mapper ): Stream {
        return $this->bind( curry( traversable_map, 2 )( $mapper ) );
    }


    /**
     * Flatten the stream.
     *
     * @param bool $preserve_keys preserve original keys (optional, default is false)
     * @return Stream a stream
     * @see \convert\traversable_flatten
     */
    public function flatten( bool $preserve_keys = false ): Stream {
        return $this->bind( curry( reverse( traversable_flatten ), 2 )( $preserve_keys ) );
    }


    /**
     * Filter the stream.
     *
     * @param callable $predicate a predicate to filter the stream
     * @return Stream a stream
     * @see \convert\traversable_filter
     */
    public function filter( callable $predicate ): Stream {
        return $this->bind( curry( traversable_filter, 2 )( $predicate ) );
    }


    /**
     * Remove duplicate values from the stream
     *
     * @param string $sort_flags sorting flags (optional, default is SORT_REGULAR)
     * @return Stream a stream
     * @see array_unique
     */
    public function distinct( $sort_flags = SORT_REGULAR ): Stream {
        return $this->bind( curry( reverse( array_unique ), 2 )( $sort_flags ) );
    }


    /**
     * Exchange all keys with their associated values in the stream.
     *
     * @return Stream a stream
     * @see array_flip
     */
    public function flip(): Stream {
        return $this->bind( array_flip );
    }


    /**
     * Extract a slice of the stream.
     *
     * @param int $offset offset
     * @param int $length length (optional, default is null)
     * @param bool $preserve_keys preserve keys (optional, default is false)
     * @return Stream a stream
     * @see array_slice
     */
    public function slice( int $offset, $length = null, bool $preserve_keys = false ): Stream {
        return $this->bind( curry( reverse( array_slice ), 4 )
                ( $preserve_keys )
                ( null === $length ? $length : intval( $length ) )
                ( $offset )
        );
    }


    /**
     * Extract a slice of the stream.
     *
     * @param int $max_size max size
     * @return Stream a stream
     * @see array_slice
     */
    public function limit( int $max_size ): Stream {
        return $this->slice( 0, $max_size );
    }


    /**
     * Extract a slice of the stream.
     *
     * @param int $number number of elements to skip
     * @return Stream a stream
     * @see array_slice
     */
    public function skip( int $number ): Stream {
        return $this->slice( $number - 1 );
    }


    /**
     * Merge this stream with one or more traversables.
     *
     * @param array|\Traversable ...$traversables the traversables
     * @return Stream a stream
     * @see \convert\traversable_merge
     */
    public function merge( ...$traversables ): Stream {
        $merger = curry( traversable_merge, count( $traversables ) + 1 );
        traversable_walk( $merger, $traversables );
        return $this->bind( $merger );
    }


    /**
     * Sort the stream.
     *
     * @param callable|int $flags_or_comparator either one of SORT_* constants
     * or a comparator function (optional, default is SORT_REGULAR)
     * @return Stream a stream
     * @see \convert\traversable_sort
     */
    public function sort( $flags_or_comparator = SORT_REGULAR ): Stream {
        return $this->bind( curry( traversable_sort, 2 )( $flags_or_comparator ) );
    }


    /**
     * Reverse the order of elements of the stream.
     *
     * @param bool $preserve_keys preserve original keys (optional, default is false)
     * @return Stream a stream
     * @see array_reverse
     */
    public function reverse( bool $preserve_keys = false ): Stream {
        return $this->bind( curry( reverse( array_reverse ), 2 )( $preserve_keys ) );
    }


    /**
     * Randomize the order of elements of the stream.
     *
     * @return Stream a stream
     * @see \convert\traversable_randomize
     */
    public function randomize(): Stream {
        return $this->bind( traversable_randomize );
    }


    /* Monad::bindMonad() shorthands */


    private function find( $predicate, array $array ) {
        foreach( $array as $key => $item ) {
            if( null === $predicate || call_user_func( $predicate, $value, $key ) ) {
                return $item;
            }
        }
        return null;
    }


    /**
     * Find first element of the stream (optionally matched with predicate).
     *
     * This is a closing operation (it calls <code>$this->close()</code> internally).
     *
     * @param callable $predicate the predicate (optional)
     * @return Optional an optional result
     * @see \monad\Stream::close()
     */
    public function findFirst( callable $predicate = null ): Optional {
        return $this->bindMonad( sequence(
                curry( [$this, 'find'], 2 )( $predicate ),
                optional
        ) );
    }


    /**
     * Find last element of the stream (optionally matched with predicate).
     *
     * This is a closing operation (it calls <code>$this->close()</code> internally).
     *
     * @param callable $predicate the predicate (optional)
     * @return Optional an optional result
     * @see \monad\Stream::close()
     */
    public function findLast( callable $predicate = null ): Optional {
        return $this->bindMonad( sequence(
                curry( reverse( array_reverse ) )( true ),
                curry( [$this, 'find'], 2 )( $predicate ),
                optional
        ) );
    }


    /**
     * Find random element of the stream (optionally matched with predicate).
     *
     * This is a closing operation (it calls <code>$this->close()</code> internally).
     *
     * @param callable $predicate the predicate (optional)
     * @return Optional an optional result
     * @see \monad\Stream::close()
     */
    public function findRandom( callable $predicate = null ): Optional {
        return $this->bindMonad( sequence(
                null === $predicate
                    ? conditionally(
                        negation( is_empty ),
                        array_value_getter( array_rand( $this->val() ) )
                    )
                    : sequence(
                        traversable_randomize,
                        curry( [$this, 'find'], 2 )( $predicate )
                    ),
                optional
        ) );
    }


    private function findExtreme( $comparator, bool $is_max, array $array ) {

        if( empty( $array ) ) {
            return null;
        }

        if( null === $comparator ) {
            return $is_max ? max( $array ) : min( $array );
        }

        $extreme = array_shift( $array );
        foreach( $array as $item ) {
            $comarison = $comparator( $item, $extreme );
            if( ( $is_max && 0 < $comarison ) || ( !$is_max && 0 > $comarison ) ) {
                $extreme = $item;
            }
        }
        return $extreme;
    }


    /**
     * Find minimum element of the stream (optionally compared by comparator).
     *
     * This is a closing operation (it calls <code>$this->close()</code> internally).
     *
     * @param callable $comparator the comparator (optional)
     * @return Optional an optional result
     * @see \monad\Stream::close()
     */
    public function findMin( callable $comparator = null ): Optional {
        return $this->bindMonad( sequence(
                curry( [$this, 'findExtreme'], 3 )
                    ( $comparator )
                    ( false ),
                optional
        ) );
    }


    /**
     * Find maximum element of the stream (optionally compared by comparator).
     *
     * This is a closing operation (it calls <code>$this->close()</code> internally).
     *
     * @param callable $comparator the comparator (optional)
     * @return Optional an optional result
     * @see \monad\Stream::close()
     */
    public function findMax( callable $comparator = null ): Optional {
        return $this->bindMonad( sequence(
                curry( [$this, 'findExtreme'], 3 )
                    ( $comparator )
                    ( true ),
                optional
        ) );
    }


    /**
     * Get the sum of the stream.
     *
     * This is a closing operation (it calls <code>$this->close()</code> internally).
     *
     * @return Optional an optional result
     * @see \monad\Stream::close()
     */
    public function sum(): Optional {
        return $this->bindMonad( sequence(
                conditionally( negation( is_empty ), array_sum ),
                optional
        ) );
    }


    private function ifMatch( callable $matcher, callable $predicate, callable $action ): Optional {
        return $this->bindMonad( sequence(
                conditionally(
                    curry( $matcher, 2 )( $predicate ),
                    $action
                ),
                optional
        ) );
    }


    /**
     * Perform an action if all elements of the stream match the predicate,
     * and return its optional result.
     *
     * Current value of the stream will be passed to the action as an argument.
     *
     * This is a closing operation (it calls <code>$this->close()</code> internally).
     *
     * @param callable $predicate the predicate
     * @param callable $action the action
     * @return Optional the optional result of the action
     * @see \monad\Stream::close()
     */
    public function ifAllMatch( callable $predicate, callable $action ): Optional {
        return $this->ifMatch( all_match, $predicate, $action );
    }


    /**
     * Perform an action if any element of the stream matches the predicate,
     * and return its optional result.
     *
     * Current value of the stream will be passed to the action as an argument.
     *
     * This is a closing operation (it calls <code>$this->close()</code> internally).
     *
     * @param callable $predicate the predicate
     * @param callable $action the action
     * @return Optional the optional result of the action
     * @see \monad\Stream::close()
     */
    public function ifAnyMatch( callable $predicate, callable $action ): Optional {
        return $this->ifMatch( any_match, $predicate, $action );
    }


    /**
     * Perform an action if no elements of the stream match the predicate,
     * and return its optional result.
     *
     * Current value of the stream will be passed to the action as an argument.
     *
     * This is a closing operation (it calls <code>$this->close()</code> internally).
     *
     * @param callable $predicate the predicate
     * @param callable $action the action
     * @return Optional the optional result of the action
     * @see \monad\Stream::close()
     */
    public function ifNoneMatch( callable $predicate, callable $action ): Optional {
        return $this->ifMatch( none_match, $predicate, $action );
    }


    /**
     * Iteratively reduce the stream to a single value using a callback function.
     *
     * Method applies iteratively the callback function to elements and keys of
     * the stream, so as to reduce it to a single value.
     *
     * This is a closing operation (it calls <code>$this->close()</code> internally).
     *
     * @param callable $callback the callback to apply to each element and key;
     * current intermediate result will be passed as the first argument
     * (in the case of the first iteration it instead holds the value of $initial),
     * current value will be passed as the second one, current key will be passed
     * as the third one; callback must return new intermediate result
     * @param mixed $initial initial value (optional, default is NULL)
     * @return Optional an optional result
     * @see \convert\traversable_reduce
     * @see \monad\Stream::close()
     */
    public function reduce( callable $callback, $initial = null ): Optional {
        return $this->bindMonad( sequence(
                curry( traversable_reduce, 3 )( $callback )( $initial ),
                optional
        ) );
    }


    /* Monad::extract() shorthands */


    /**
     * Get statistics of the stream.
     *
     * This is a closing operation (it calls <code>$this->close()</code> internally).
     *
     * @param callable|int $flags_or_comparator either one of SORT_* constants
     * or a comparator function (optional, default is SORT_REGULAR)
     * @return Statistics the statistics of the stream
     * @see \meta\Statistics
     * @see \monad\Stream::close()
     */
    public function statistics( $flags_or_comparator = SORT_REGULAR ): Statistics {
        return statistics( $this->extract(), $flags_or_comparator );
    }


    /**
     * Apply an action to each element of the stream.
     *
     * This is a closing operation (it calls <code>$this->close()</code> internally).
     *
     * @param callable $action the action
     * @see \convert\traversable_walk
     * @see \monad\Stream::close()
     */
    public function each( callable $action ) {
        traversable_walk( $action, $this->extract() );
    }


    private function match( callable $matcher, callable $predicate ): bool {
        return $matcher( $predicate, $this->extract() );
    }


    /**
     * Do all elements of the stream match the predicate.
     *
     * This is a closing operation (it calls <code>$this->close()</code> internally).
     *
     * @param callable $predicate the predicate
     * @return bool true if all elements of the stream match the predicate
     * @see \meta\all_match
     * @see \monad\Stream::close()
     */
    public function allMatch( callable $predicate ): bool {
        return $this->match( all_match, $predicate );
    }


    /**
     * Does at least one element of the stream match the predicate.
     *
     * This is a closing operation (it calls <code>$this->close()</code> internally).
     *
     * @param callable $predicate the predicate
     * @return bool true if at least one element of the stream matches the predicate
     * @see \meta\any_match
     * @see \monad\Stream::close()
     */
    public function anyMatch( callable $predicate ): bool {
        return $this->match( any_match, $predicate );
    }


    /**
     * Does no one element of the stream match the predicate.
     *
     * This is a closing operation (it calls <code>$this->close()</code> internally).
     *
     * @param callable $predicate the predicate
     * @return bool true if no one element of the stream matches the predicate
     * @see \meta\none_match
     * @see \monad\Stream::close()
     */
    public function noneMatch( callable $predicate ): bool {
        return $this->match( none_match, $predicate );
    }


    /**
     * Get count of elements of the stream.
     *
     * This is a closing operation (it calls <code>$this->close()</code> internally).
     *
     * @param string $mode a mode; either COUNT_NORMAL or COUNT_RECURSIVE
     * (optional, default is COUNT_NORMAL)
     * @return int count of elements of the stream
     * @see count
     * @see \Countable
     * @see \monad\Stream::close()
     */
    public function count( $mode = COUNT_NORMAL ): int {
        return count( $this->extract(), $mode );
    }


    /**
     * Is the stream empty.
     *
     * This is a closing operation (it calls <code>$this->close()</code> internally).
     *
     * @return bool true of the stream is empty
     * @see \monad\Stream::close()
     */
    public function isEmpty(): bool {
        return empty( $this->extract() )
    }


    /**
     * Get the items of the stream as an array.
     *
     * This is a closing operation (it calls <code>$this->close()</code> internally).
     *
     * @return array the items of the stream as an array
     * @see \monad\Stream::close()
     */
    public function toArray(): array {
        return $this->extract();
    }


    /**
     * {@inheritDoc}
     *
     * This is a closing operation (it calls <code>$this->close()</code> internally).
     *
     * @see \monad\Monad::extract()
     * @see \monad\Stream::close()
     */
    public function extract(): array {
        $value = parent::extract();
        $this->close();
        return $value;
    }


    /* Closing operation */


    /**
     * Invoke all close handlers.
     *
     * Current value of the stream will be passed to each handler,
     * result of a handler will be ignored.
     */
    public function close() {
        $val = $this->val();
        foreach( $this->close_handlers as $handler ) {
            $handler( $val );
        }
    }

}
