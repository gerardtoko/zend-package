<?php

/*
 * This file is part of the Factory package.
 *
 * (c) Gerard Toko <gerardtoko@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class App_Form extends Zend_Form {

    protected $_translate = null;

    public function __construct($options = null) {

        if (Zend_Registry::isRegistered('translate')) {
            $this->_translate = Zend_Registry::get('translate');
        }

        if (is_array($options)) {
            $this->setOptions($options);
        } elseif ($options instanceof Zend_Config) {
            $this->setConfig($options);
        }

        // Extensions...
        $this->init();
        $this->loadDefaultDecorators();
    }

    protected function t($value) {

        if (!is_null($this->_translate)) {
            return $this->_translate->_($value);
        }
    }

}