<?php

/*
 * This file is part of the Factory package.
 *
 * (c) Gerard Toko <gerardtoko@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class App_Helper_T extends Zend_View_Helper_Abstract {

    /**
     *
     * @param type $value
     * @return type 
     */
    public function T($value) {
        if (Zend_Registry::isRegistered('translate')) {
            $translate = Zend_Registry::get("translate");
            return $translate->_($value);
        }
    }
}