<?php

/*
 * This file is part of the Factory package.
 *
 * (c) Gerard Toko <gerardtoko@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class Plugin_Admin_auth extends Zend_Controller_Plugin_Abstract {

    /**
     *
     * @param Zend_Controller_Request_Abstract $request 
     */
    public function dispatchLoopStartup(Zend_Controller_Request_Abstract $request) {

        $bundle = $this->getRequest()->getParam('bundle');
        $config_app = Zend_Registry::get('config_app');

        $view = Zend_Registry::get('views');
        $view->projetName = $config_app['projet_name'];

        if ($bundle == $config_app['admin_name'] || $bundle == $config_app['bundles']['all'][1]) {

            $session = Zend_Registry::get('session_app');
            $user_session = $session->getUser();

            if ($user_session === null || $user_session['status'] == 0) {

                $_SERVER['PHP_AUTH_USER'] == null;
                $_SERVER['PHP_AUTH_PW'] == null;

                $redirect = sprintf("%s/%s/admin_connect", Zend_Controller_Front::getInstance()->getBaseUrl(), $config_app['bundles']['all'][0]);
                $r = Zend_Controller_Action_HelperBroker::getStaticHelper('redirector');
                $r->gotoUrl($redirect)->redirectAndExit();
            } else {

                $user_model = App_Ressource::getModel('admin/user');
                $auth_data_db = $user_model->findOne($user_session[$user_model->getNameCol(0)]);

                if ($user_session['user_id'] == $auth_data_db['user_id'] &&
                        $user_session['username'] == $auth_data_db['username']
                ) {
                    Zend_Registry::set('is_login', true);
                    $view->projetName = $config_app['projet_name'];
                } else {

                    $_SERVER['PHP_AUTH_USER'] == null;
                    $_SERVER['PHP_AUTH_PW'] == null;

                    Zend_Session::destroy();
                    $r = Zend_Controller_Action_HelperBroker::getStaticHelper('redirector');
                    $r->gotoUrl(Zend_Controller_Front::getInstance()->getBaseUrl())->redirectAndExit();
                }
            }
        }
    }

}