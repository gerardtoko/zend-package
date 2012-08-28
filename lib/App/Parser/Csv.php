<?php

/*
 * This file is part of the Factory package.
 *
 * (c) Gerard Toko <gerardtoko@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class Csv {

    /**
     *
     * @param array $array
     * @param type $file
     */
    public static function dump(array $array, $file) {

        ob_start();
        $encode = new Csv_Dumper($array);
        $encode->encode();
        $output = ob_get_clean();

        if (preg_match('#\.csv?#', $file)) {
            $file = fopen($file, "w");
            $lines = fwrite($file, $output);
            fclose($file);
            return $lines;
        }
    }


    /**
     *
     * @param type $file
     * @return type
     */
    public static function load($file){
        if(preg_match('#\.csv?#', $file)){
           $decoder = new Csv_Loader($file);
           $array = $decoder->decode();
           return $array;
        }
    }
}