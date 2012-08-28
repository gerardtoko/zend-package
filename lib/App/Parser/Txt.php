<?php

/*
 * This file is part of the Factory package.
 *
 * (c) Gerard Toko <gerardtoko@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class App_Parser_Txt {

    const COMMENT_PATTERN = '#^\##';
    
    /**
     *
     * @param type $file
     * @return type
     */
    public static function load($file) {
        $lines = file($file);
        $array = self::decode($lines);
        return $array;
    }
    
    /**
     *
     * @param type $lines
     * @param type $indent
     * @return array
     */
    private static function decode($lines) {

        $parse = array();
        foreach ($lines as $line) {
            $line = trim($line); 
            if (!preg_match(self::COMMENT_PATTERN, $line) && $line != "") {
                $parse[] = $line;
            }
        }
        return $parse;
    }

}