<?php

/*
 * This file is part of the Factory package.
 *
 * (c) Gerard Toko <gerardtoko@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class App_Parser_Yaml {

    /**
     *
     * @param array $array
     * @param type $file
     */
    public static function dump(array $array, $file) {

        ob_start();
        App_Parser_Yaml_Dumper::encode($array);
        $output = ob_get_clean();

        if (preg_match('#\.yml?#', $file)) {
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
        if(preg_match('#\.yml?#', $file)){
           $lines = file($file);
           $array = App_Parser_Yaml_Loader::decode($lines, 0);
           return $array;
        }
    }
}