<?php

/*
 * This file is part of the Factory package.
 *
 * (c) Gerard Toko <gerardtoko@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

abstract class App_Event_Dispatcher {

    
    protected static $_execution = true;

    /**
     *
     * @param type $notif
     * @param type $event
     * @return boolean 
     */
    public static function dispatch($notif, &$event) {

        foreach ($notif as $value) {

            if (self::$_execution == false) {
                break;
            } else {
                if (!empty($value[0]) && !empty($value[1])) {

                    $meth = $value[1];
                    $val = $value[0];

                    if (is_object($val)) {
                        $object = $val;
                        $object->$meth($event);
                    }

                    if (is_string($val)) {
                        $object = App_Ressource::getEvent($val);
                        $object::$meth($event);
                    }
                }
            }
        }
        
        self::$_execution = true;
        return true;
    }

    public static function stop() {
        self::$_execution = false;
    }

}