<?php

/*
 * This file is part of the Factory package.
 *
 * (c) Gerard Toko <gerardtoko@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class Plugin_Maintenance extends Zend_Controller_Plugin_Abstract {

    protected $_request;
    protected $_front;

    /**
     *
     * @param Zend_Controller_Request_Abstract $request 
     */
    public function dispatchLoopStartup(Zend_Controller_Request_Abstract $request) {

        $this->_front = Zend_Controller_Front::getInstance();
        $this->_request = $request;

        $bundle = $this->getRequest()->getParam('bundle');
        $config_app = Zend_Registry::get('config_app');

        if ($bundle == $config_app['bundles']['all'][0]) {

            $file = APPLICATION_PATH . "/configs/maintenance.yml";
            if (file_exists($file)) {
                
                $maintenance_config = App_Config::load($file);

                if (!empty($maintenance_config['maintenance']['flag']) && !empty($maintenance_config['maintenance']['authorized'])) {

                    if ($maintenance_config['maintenance']['flag'] = true) {

                        if (!in_array($_SERVER['REMOTE_ADDR'], $maintenance_config['maintenance']['authorized'])) {   

                            $this->_request->clearParams();
                            $array_bundle = array('module' => 'error', 'controller' => 'index', 'action' => 'maintenance');
                            foreach ($array_bundle as $key => $value) {
                                $meth = sprintf('set%sName', ucfirst($key));
                                $this->_request->$meth($value);
                                $this->_request->setParam($key, $value);
                            }
                            $this->_request->setParam('bundle', $config_app['bundles']['all'][0]);
                            $this->_front->setRequest($this->_request);
                            $this->_front->getRouter()->setFrontController($this->_front);
                        }
                    }
                }
            }
        }
    }

}