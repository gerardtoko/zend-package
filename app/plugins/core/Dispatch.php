<?php

/*
 * This file is part of the Factory package.
 *
 * (c) Gerard Toko <gerardtoko@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class Plugin_Dispatch extends Zend_Controller_Plugin_Abstract {

    protected $_request;
    protected $_front;
    protected $_replace = array('#', '!', '^', '$', '(', ')', '[', ']', '{', '}', '?', '+', '*', '.', '\\', '|');

    /**
     *
     * @param Zend_Controller_Request_Abstract $request 
     */
    public function routeShutdown(Zend_Controller_Request_Abstract $request) {

        $config_app = Zend_Registry::get('config_app');
        $this->_front = Zend_Controller_Front::getInstance();
        $this->_request = $request;

        if ($this->_routeRewrite() == false) {

            //Controll URI
            $uri = Zend_Registry::get('req_uri');
            $splits = explode('/', trim($uri, '/'));
            $bundle = $splits[0];
            $front_pass = $this->_controlPass($splits);

            if ($front_pass == false) {
                if ($bundle == $config_app['admin_name'] || $bundle == $config_app['bundles']['all'][1]) {

                    $array_request = array('bundle', 'module', 'controller', 'action');

                    foreach ($array_request as $key => $value) {
                        if (!empty($splits[$key])) {
                            $this->_request->setParam($value, $splits[$key]);
                        } else {
                            if ('module' == $value) {
                                if ($this->_request->getParam('bundle') == $config_app['bundles']['all'][0]) {
                                    $this->_request->setParam($value, $config_app['bundles']['default'][$value][0]);
                                } else {
                                    $this->_request->setParam($value, $config_app['bundles']['default'][$value][1]);
                                }
                            } else {
                                $this->_request->setParam($value, $config_app['bundles']['default'][$value]);
                            }
                        }
                    }

                    if ($this->_request->getParam('bundle') == $config_app['admin_name']) {
                        $this->_request->setParam('bundle', $config_app['bundles']['all'][1]);
                    }

                    $array_bundle = array('module', 'controller', 'action');
                    foreach ($array_bundle as $value) {
                        $meth = sprintf('set%sName', ucfirst($value));
                        $this->_request->$meth($this->_request->getParam($value));
                    }
                } else {

                    $this->_request->clearParams();
                    $array_bundle = array('module' => $config_app['default_module'], 'controller' => 'index', 'action' => 'index');
                    foreach ($array_bundle as $key => $value) {
                        $meth = sprintf('set%sName', ucfirst($key));
                        $this->_request->$meth($value);
                        $this->_request->setParam($key, $value);
                    }
                    $this->_request->setParam('bundle', $config_app['bundles']['all'][0]);
                }
            } else {
                $this->_request->setParam('bundle', $config_app['bundles']['all'][0]);
            }


            $this->_front->setRequest($this->_request);
            $this->_front->getRouter()->setFrontController($this->_front);
        }
    }

    /**
     *
     * @return boolean 
     */
    protected function _routeRewrite() {

        $file = APPLICATION_PATH . '/configs/rewrite.yml';
        if (file_exists($file)) {

            $config_rewrite = App_Config::load($file);

            if (Zend_Registry::isRegistered('req_uri')) {

                $uri = Zend_Registry::get('req_uri');
                foreach ($this->_replace as $value) {
                    $this->_replace[$value] = "\$value";
                }
                $uri = strtr(substr($uri, 1), $this->_replace);

                foreach ($config_rewrite as $path => $route) {
                    if (preg_match("#^$path#", $uri)) {

                        $this->_request->clearParams();
                        foreach ($route as $key => $value) {
                            if ($key != 'bundle') {
                                $meth = sprintf('set%sName', ucfirst($key));
                                $this->_request->$meth($value);
                                $this->_request->setParam($key, $value);
                            } else {
                                $this->_request->setParam($key, $value);
                            }
                        }

                        $html = new App_Route_Html();
                        $params = $html->setParams(explode('/', trim($uri, '/')));
                        $this->_request->setParams($params);
                        $this->_front->setRequest($this->_request);
                        $this->_front->getRouter()->setFrontController($this->_front);
                        return true;
                    }
                }
            }
        }
    }

    /**
     *
     * @param type $splits
     * @return boolean
     * @throws Exception 
     */
    protected function _controlPass($splits) {


        if (!empty($splits[0])) {

            $module = $splits[0];
            $config_app = Zend_Registry::get('config_app');
            if ($module != $config_app['admin_name'] || $module != $config_app['bundles']['all'][1]) {

                $file = sprintf('%s/shemas/%s.yml', APPLICATION_PATH, $config_app['bundles']['all'][0]);
                if (file_exists($file)) {

                    $config_shema = App_Config::load($file);
                    $front_pass = $config_shema['front_pass'];

                    if (in_array($module, $front_pass)) {
                        $result = true;
                    } else {
                        $result = false;
                    }
                } else {
                    throw new Exception("$file no exist");
                }
            } else {
                $result = true;
            }
        } else {
            $result = false;
        }

        return $result;
    }

}