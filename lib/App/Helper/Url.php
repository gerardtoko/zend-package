<?php

/*
 * This file is part of the Factory package.
 *
 * (c) Gerard Toko <gerardtoko@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class App_Helper_Url extends Zend_View_Helper_Abstract {

    /**
     *
     * @param type $module
     * @param array $value
     * @param type $bundle
     * @return type 
     */
    public function Url($module = null, array $value = null, $bundle = null) {

        if (Zend_Registry::isRegistered('reqContext') && Zend_Registry::isRegistered('config_app')) {
            $req = Zend_Registry::get('reqContext');
            $config_app = Zend_Registry::get('config_app');

            $bundle = !(is_null($bundle)) ? $bundle : $req['bun'];

            //base and bundle
            $url = sprintf('%s/%s', $req['bas'], $bundle);

            //module
            $url = !(is_null($module)) ? sprintf('%s/%s', $url, $module) : sprintf('%s/%s', $config_app['default_module']);

            //controller
            $url = isset($value['controller']) ? sprintf('%s/%s', $url, $value['controller']) : $url;
            //action
            $url = isset($value['action']) ? sprintf('%s/%s', $url, $value['action']) : $url;
            return $url;
        }
    }

}