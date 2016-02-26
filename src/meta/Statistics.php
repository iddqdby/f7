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

namespace meta;

use function convert\traversable_sort;
use function convert\to_array;


/**
 * Various statistics of an array.
 */
class Statistics {
    
    private $count = 0;
    private $min = 0.;
    private $max = 0.;
    private $sum = 0.;
    private $average = 0.;
    private $median_ceil = 0.;
    private $median_average = 0.;
    private $median_floor = 0.;
    private $count_is_even = true;
    private $count_is_odd = false;
    
    
    /**
     * Calculate statistics for a traversable.
     * 
     * @param array $traversable the traversable
     * @param string $flags_or_comparator either one of SORT_* constants
     * or a comparator function
     */
    public function __construct( $traversable, $flags_or_comparator = SORT_REGULAR ) {
        
        $values = traversable_sort( $flags_or_comparator, array_values( to_array( $traversable ) ) );
        
        if( empty( $values ) ) {
            return;
        }
        
        $this->count = count( $values );
        $this->min = (float)$values[0];
        $this->max = (float)$values[ $this->count - 1 ];
        $this->sum = (float)array_sum( $values );
        $this->average = (float)( $this->sum / $this->count );
        $this->count_is_odd = (bool)( $this->count % 2 );
        $this->count_is_even = !$this->count_is_odd;
        $median_floor_key = (int)floor( $this->count / 2 ) - 1 + (int)$this->count_is_odd;
        $median_ceil_key = $median_floor_key + (int)$this->count_is_even;
        $this->median_floor = (float)$values[ $median_floor_key ];
        $this->median_ceil = (float)$values[ $median_ceil_key ];
        $this->median_average = $this->count_is_odd ? $this->median_floor : (float)( ( $this->median_floor + $this->median_ceil ) / 2 );
    }
    
    /**
     * Get count.
     * 
     * @return int count
     */
    public function getCount(): int {
        return $this->count;
    }
    
    /**
     * Get min.
     * 
     * @return float min
     */
    public function getMin(): float {
        return $this->min;
    }
    
    /**
     * Get max.
     * 
     * @return float max
     */
    public function getMax(): float {
        return $this->max;
    }
    
    /**
     * Get sum.
     * 
     * @return float sum
     */
    public function getSum(): float {
        return $this->sum;
    }
    
    /**
     * Get average.
     * 
     * @return float average
     */
    public function getAverage(): float {
        return $this->average;
    }
    
    /**
     * Get median ceil.
     * 
     * @return float median ceil
     */
    public function getMedianCeil(): float {
        return $this->median_ceil;
    }
    
    /**
     * Get median average.
     * 
     * @return float median average
     */
    public function getMedianAverage(): float {
        return $this->median_average;
    }
    
    /**
     * Get medain floor.
     * 
     * @return float median floor
     */
    public function getMedianFloor(): float {
        return $this->median_floor;
    }
    
    /**
     * Is count even.
     * 
     * @return boolean true if count is even
     */
    public function isCountEven(): bool {
        return $this->count_is_even;
    }
    
    /**
     * Is count odd.
     * 
     * @return boolean true if count is odd
     */
    public function isCountOdd(): bool {
        return $this->count_is_odd;
    }

}
