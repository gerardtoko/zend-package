<?php

/*
 * This file is part of the Factory package.
 *
 * (c) Gerard Toko <gerardtoko@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class App_Helper_Base extends Zend_View_Helper_Abstract {

  /**
   *
   * @param type $route
   * @return type
   * @throws Exception 
   */
    public function Base($route = null) {
        if (Zend_Registry::isRegistered('reqContext') && !$route) {
            $req = Zend_Registry::get('reqContext');
            $route = sprintf('%s/', $req['bas']);
        } else {
            $file = APPLICATION_PATH . '/configs/app.yml';
            if (file_exists($file)) {
                $config = App_Config::load($file);
                $route = $config['base'];
            }else{
                throw new Exception("$file no exist");
            }
        }
        return $route;
    }

}