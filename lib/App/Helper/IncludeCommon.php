<?php

/*
 * This file is part of the Factory package.
 *
 * (c) Gerard Toko <gerardtoko@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class App_Helper_IncludeCommon extends Zend_View_Helper_Abstract {

    /**
     *
     * @param type $value
     * @return type
     * @throws Exception 
     */
    public function IncludeCommon($value) {

        if (Zend_Registry::isRegistered("views")) {
            $views = Zend_Registry::get("views");
            return $views->render(sprintf('common/%s', $value));
        }
    }

}