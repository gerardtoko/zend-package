<?php

/*
 * This file is part of the Factory package.
 *
 * (c) Gerard Toko <gerardtoko@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class Csv_Dumper {

    protected $_lines = null;

    /**
     *
     * @param type $lines 
     */
    public function __construct($lines) {
        $this->_lines = $lines;
    }

    
    /**
     * 
     */
    public function encode() {
        foreach ($this->_lines as $line) {
            $line_value = "";
            foreach ($line as $value) {
                $line_value .= sprintf("%s;", $value);
            }
            echo sprintf("%s\n", substr($line_value, 0, -1));
        }
    }

}