<?php

/*
 * This file is part of the Factory package.
 *
 * (c) Gerard Toko <gerardtoko@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
class App_Generator {


    // Tableau lettre miniscule
    protected $_alpha = array("a", "b", "c", "d", "e", "f","g", "h", "i", "j", "k", "l","m",
                    "n", "o", "p", "q", "r","s", "t", "u", "v", "w", "x","y","z");

    // Tableau lettre Majuscule
    protected $_ALPHA = array("A", "B", "C", "D", "E", "F","G", "H", "I", "J", "K", "L","M",
                    "N", "O", "P", "Q", "R","S", "T", "U", "V", "W", "X","Y","Z");

    // Tableau number
    protected $_Number = array( 0, 1, 2, 3, 4, 5, 6, 7, 8, 9);

    // Tableau de melange
    protected  $Array = null;


    public function __construct () {}
    public function __clone () {}


    /**
     *
     * @param type $count
     * @return \App_Generator 
     */
    public function Number($count = 1){

        if(is_null($this->Array)){
            $this->Array = $this->_Number;
         } else {
             for($i = 1; $i <= $count ; $i++){
                $this->Array = array_merge($this->Array, $this->_Number);
             }
         }
        return $this;
    }


    /**
     *
     * @param type $count
     * @return \App_Generator 
     */
    public function AlphaMaj($count= 1){
         if(is_null($this->Array)){
            $this->Array = $this->_ALPHA;
         } else {
             for($i = 1; $i <= $count ; $i++){
                $this->Array = array_merge($this->Array, $this->_ALPHA);
             }
         }
        return $this;
    }


    /**
     *
     * @param type $count
     * @return \App_Generator 
     */
    public function AlphaMin($count= 1){
        if(is_null($this->Array)){
            $this->Array = $this->_alpha;
         } else {
             for($i = 1; $i <= $count ; $i++){
                $this->Array = array_merge($this->Array, $this->_alpha);
            }
         }
        return $this;
    }


    /**
     *
     * @return \App_Generator 
     */
    public function All(){
        $this->Array = array_merge($this->_ALPHA, $this->_alpha, $this->_Number);
        return $this;
    }




    /**
     *
     * @param type $count
     * @return type 
     */
    public function Code($count= 6){

        // Mixage des elements
        $arrayMixed = $this->mixer($this->Array);

        // Recuperation des elements
        $Ramdom = array_rand($arrayMixed, $count);

        // Array final
        $Array_Final = "";

        foreach($Ramdom as $i) {
            $Array_Final .= $this->Array[$i];
        }

        return $Array_Final;
    }



    /**
     *
     * @param type $array
     * @return type 
     */
    private function mixer($array) {

        // Tableau vide
        $val = array();
        $keys = array_keys($array);
        shuffle($keys);
            foreach($keys as $key) {
                $val[] = $array[$key];
                }
            return $val;
     }
}