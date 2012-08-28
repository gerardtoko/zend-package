<?php

/*
 * This file is part of the Factory package.
 *
 * (c) Gerard Toko <gerardtoko@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class Plugin_Context extends Zend_Controller_Plugin_Abstract {

    protected $view;
    protected $front;

    /**
     *
     * @param Zend_Controller_Request_Abstract $request
     * @throws Exception 
     */
    public function dispatchLoopStartup(Zend_Controller_Request_Abstract $request) {

        if (Zend_Registry::isRegistered('views') == false || !Zend_Registry::isRegistered('config_app') == false) {
            $this->view = Zend_Registry::get("views");
            $config_app = Zend_Registry::get('config_app');
        } else {
            throw new Exception('views and config_app no present in Zend_Registry');
        }

        //Element
        $this->front = Zend_Controller_Front::getInstance();

        $base = $this->front->getBaseUrl();
        $mod = $this->front->getRequest()->getModuleName();
        $con = $this->front->getRequest()->getControllerName();
        $act = $this->front->getRequest()->getActionName();

        
        $bundle_param = $this->front->getRequest()->getParam('bundle');
        if ($config_app['admin_name'] == $bundle_param || $config_app['bundles']['all'][1] == $bundle_param) {
            $bun = $config_app['admin_name'];
        } else {
            $bun = $this->front->getRequest()->getParam('bundle');
        }

        //On stock dans le registry
        $req_context = array('bas' => $base, 'bun' => $bun, 'mod' => $mod, 'con' => $con, 'act' => $act);

        Zend_Registry::set('reqContext', $req_context);
        
        $act_title = ($act == "index") ?  "home" :  $act;
        $con_title = ($con == "index") ?  $mod :  $con;
        $this->view->headTitle(sprintf("%s %s | %s", ucfirst($act_title) ,  ucfirst($con_title), $config_app["projet_name"]));

        //SubActon
        $this->view->base = strtolower(sprintf('%s/%s/%s/%s', $base, $bun, $mod, $con));

        $this->view->nameController = $con;

        //Redirection
        $redirect = strtolower(sprintf('%s/%s/%s/%s', $base, $bun, $mod, $con));
        Zend_Registry::set("redBase", $redirect);

        $tran = array('/' => '_');
        $cache_name = strtr(trim($this->view->base, '/'), $tran);
        Zend_Registry::set('cache_name', $cache_name);
        //Chargement des ressources
        App_Loader_Ressource::pushRessources();
    }

}