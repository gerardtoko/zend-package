<?php

/*
 * This file is part of the Factory package.
 *
 * (c) Gerard Toko <gerardtoko@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class App_Helper_Skin extends Zend_View_Helper_Abstract {

    /**
     *
     * @param type $route
     * @return type
     * @throws Exception 
     */
    public function Skin($route) {
        $req = Zend_Registry::get('reqContext');
        $config_app = Zend_Registry::get('config_app');
        $bundle = $req['bun'];


        if ($bundle == $config_app['admin_name']) {
            $bundle = $config_app['bundles']['all'][0];
        }

        $file = sprintf('%s/bundles/%s/etc/template.yml', APPLICATION_PATH, $bundle);
        if (file_exists($file)) {
            $config_template = App_Config::load($file);
        } else {
            throw new Exception($file . 'no exist');
        }

        $file = sprintf('%s/web/skins/%s/%s/%s', DIR_PATH, $bundle, $config_template['current'], $route);
        if (file_exists($file)) {
            $route = sprintf('%s/web/skins/%s/%s/%s', $req['bas'], $bundle, $config_template['current'], $route);
        } else {
            $route = sprintf('%s/web/skins/%s/%s/%s', $req['bas'], $bundle, $config_template['default'], $route);
        }

        return $route;
    }

}