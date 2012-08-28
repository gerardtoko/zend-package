<?php

/*
 * This file is part of the Factory package.
 *
 * (c) Gerard Toko <gerardtoko@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class App_Helper_Ressource extends Zend_View_Helper_Abstract {

    /**
     *
     * @param type $route
     * @return type
     * @throws Exception 
     */
    public function Ressource($route) {
        $req = Zend_Registry::get('reqContext');
        $route = sprintf('%s/web/ressources/%s/%s', $req['bas'], $req['mod'], $route);
        return $route;
    }

}