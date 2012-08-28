<?php

/*
 * This file is part of the Factory package.
 *
 * (c) Gerard Toko <gerardtoko@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class App_Date {

    /**
     *
     * @param type $time
     * @return string 
     */
    public static function getFormat($time){

    $date = new Zend_Date();
    $date->sub($time, Zend_Date::TIMESTAMP);

    $difMonth = $date->get(Zend_Date::MONTH);
    if($difMonth > 1){
        $outpot = $difMonth . ' mois';
        return $outpot;
    }

    //Recuperation du jour
    $difHour = $date->get(Zend_Date::DAY);
    if($difHour > 0){
        $outpot = $difHour . ' jour';
        return $outpot;
    }


    $difHeure = $date->get(Zend_Date::HOUR);
    if($difHeure > 0){
        $outpot .=  $difHeure.'heure ';
    }

    // Recuperation des minute
    $difMin = $date->get(Zend_Date::MINUTE);
    if($difMin > 0){
        $outpot .= $difMin . 'minutes';
    }


    // Recuperation des secondes
    $difSec = $date->get(Zend_Date::SECOND);
    if($difMin == 0 && $difHeure == 0 && $difHour == 0){
        $outpot = "moins d'une minute";
    }

    //On retoure la variable
    return $outpot;
    }
}