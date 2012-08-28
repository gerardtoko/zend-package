<?php

/*
 * This file is part of the Factory package.
 *
 * (c) Gerard Toko <gerardtoko@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class App_Form_Element_Html extends Zend_Form_Element_Xhtml {

    public $helper = 'formNote';

    public function __construct($spec, $options = null) {
        $this->addPrefixPath('App_Form_Element', 'App/Form/Element/');
        parent::__construct($spec, $options);
    }

    public function loadDefaultDecorators() {
        $this->addDecorator('ViewHelper');
    }

}