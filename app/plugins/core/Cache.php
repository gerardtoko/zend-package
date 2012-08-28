<?php

/*
 * This file is part of the Factory package.
 *
 * (c) Gerard Toko <gerardtoko@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class Plugin_Cache extends Zend_Controller_Plugin_Abstract {

    /**
     *
     * @param Zend_Controller_Request_Abstract $request
     * @throws Exception 
     */
    public function dispatchLoopStartup(Zend_Controller_Request_Abstract $request) {

        // on cree une instance cache
        $file = APPLICATION_PATH . '/configs/cache.yml';
        $config_app = Zend_Registry::get('config_app');
        App_Cache::start();

        if (file_exists($file)) {
            
            $config_cache = App_Config::load($file);

            $file = APPLICATION_PATH . '/configs/env.yml';
            if (file_exists($file)) {
                $config_env = App_Config::load($file);
                $env = $config_env['env'];
            }else{
                $env = 'prod';
            }


            if ($env == 'prod' || $env == 'test') {
                if (!empty($config_cache['namespaces'])) {
                    $namespaces = $config_cache['namespaces'];
                    $space = array();
                    foreach ($namespaces as $key_namespace => $value_namespace) {
                        $space['namespace'] = $key_namespace;
                        $space['options'] = $value_namespace;
                    }
                    App_Cache::setCacheNameSpace($space);
                }
            }

            if ($env == 'dev') {
                App_Cache::setReadMode(FALSE);
                App_Cache::setWriteMode(FALSE);
            }
        }
    }

}