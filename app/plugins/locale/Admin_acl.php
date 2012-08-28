<?php
/*
 * This file is part of the Factory package.
 *
 * (c) Gerard Toko <gerardtoko@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class Plugin_Admin_acl extends Zend_Controller_Plugin_Abstract {

    /**
     *
     * @param Zend_Controller_Request_Abstract $request 
     */
    public function dispatchLoopStartup(Zend_Controller_Request_Abstract $request) {

        $bundle = $this->getRequest()->getParam('bundle');
        $config_app = Zend_Registry::get('config_app');

        if ($bundle == $config_app['admin_name'] || $bundle == $config_app['bundles']['all'][1]) {

            $session_app = Zend_Registry::get('session_app');
            $req = Zend_Registry::get('reqContext');

            $user_session = $session_app->getUser();
            $role_user = $user_session['role_id'];

            Zend_Registry::set('is_admin', false);
            $admin = false;

            foreach ($role_user as $value) {
                if ($value == 1) {
                    Zend_Registry::set('is_admin', true);
                    $admin = true;
                }
            }

            $permission = App_Ressource::getModel('admin/permission');
            $relation_permission_model = App_Ressource::getModel('admin/relation_permission');

            $conditions = array(
                'where' => array(
                    $permission->getAdapter()->quoteInto('module = ?', $req['mod']),
                    $permission->getAdapter()->quoteInto('ressource = ?', $req['con']),
                    $permission->getAdapter()->quoteInto('action = ?', $req['act'])
                )
            );

            $row_permission = $permission->findAll($conditions);
            if (!empty($row_permission[0])) {
                $conditions = array(
                    'where' => array(
                        $relation_permission_model->getAdapter()->quoteInto('permission_id = ?', $row_permission[0]['permission_id']),
                        $relation_permission_model->getAdapter()->quoteInto('role_id IN (?)', $role_user)
                    )
                );

                $row_relation_permission = $relation_permission_model->findAll($conditions);
                if (empty($row_relation_permission) && $admin == false) {
                    $bundle = $config_app['admin_name'];
                    $r = Zend_Controller_Action_HelperBroker::getStaticHelper('redirector');
                    $r->gotoUrl(sprintf('%s/%s', $req['bas'], $bundle))->redirectAndExit();
                }
            } else {
                if ($admin == false) {
                    $bundle = $config_app['admin_name'];
                    $r = Zend_Controller_Action_HelperBroker::getStaticHelper('redirector');
                    $r->gotoUrl(sprintf('%s/%s', $req['bas'], $bundle))->redirectAndExit();
                }
            }
        }
    }

}