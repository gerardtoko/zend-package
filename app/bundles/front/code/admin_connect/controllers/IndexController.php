<?php

class Admin_Connect_IndexController extends App_Controller_Action {

    public function init() {
        $this->view->moduleName = $this->getRequest()->getModuleName();
        $this->view->controllerName = $this->getRequest()->getControllerName();
        $this->view->actionName = $this->getRequest()->getActionName();

        if (!App_Ressource::moduleIsRegistered('admin')) {
            throw new Exception('le module admin_connect require le module admin');
        }
    }

    // fait appel a la vue principal
    public function indexAction() {

        $translate = Zend_Registry::get('translate');
        $form = App_Ressource::getForm('admin_connect/login');
        $space_session = Zend_Registry::get('session_app');
        $config_app = Zend_Registry::get('config_app');
        $relation_user_model = App_Ressource::getModel('admin/relation_user');
        $req = Zend_Registry::get('reqContext');
        $admin = false;
        $roles_array = array();
        $user_model = App_Ressource::getModel('admin/user');


        //title
        $this->view->headTitle($translate->_('connection'));

        if ($this->getRequest()->isPost()) {

            $form = App_Ressource::getForm('admin_connect/login');

            if ($form->isValid($_POST)) {

                $name = $form->getValue('name');
                $password = $form->getValue('password');

                $setAuth = Zend_Registry::get('auth');
                $setAuth->setIdentity($name)
                        ->setCredential($password);
                $result = $setAuth->authenticate();

                if ($result->isValid()) {

                    $auth_data = $user_model->findOne($name, 3);

                    $conditions = array(
                        'where' => $user_model->getAdapter()->quoteInto('user_id = ?', $auth_data['user_id']),
                    );

                    $row_relation_user = $relation_user_model->findAll($conditions);

                    foreach ($row_relation_user as $value) {
                        $roles_array[] = $value['role_id'];
                        if ($value['role_id'] == 1) {
                            $admin = true;
                        }
                    }

                    if ($auth_data['status'] == 1 || $admin = true) {

                        $where = array($user_model->getAdapter()->quoteInto('user_id = ?', $auth_data[$user_model->getNameCol(0)]));
                        $data_save = array("logdate" => time(), "lognum" => $auth_data['lognum'] + 1);
                        $user_model->save($data_save, $where);

                        $data = array(
                            'user_id' => $auth_data['user_id'],
                            'role_id' => $roles_array,
                            'username' => $auth_data['username'],
                            'status' => $auth_data['status'],
                            'is_administrator' => true
                        );

                        $this->dispatchEvent('onAdmin_connect_index_login_user', &$auth_data);

                        $space_session->setUser($data);
                        $bundle = $config_app['admin_name'];
                        $this->_redirect(sprintf('%s/%s', $req['bas'], $bundle));
                    } else {
                        Zend_Session::destroy();
                        $this->_redirect(Zend_Registry::get('redBase'));
                    }
                } else {

                    $values = $form->getValues();

                    foreach ($values as $element => $auth_data) {
                        $form->getElement($element)->setValue($auth_data);
                    }

                    $this->view->info = "alert-error";
                    $this->view->infoMessage = $translate->_("Nom ou Mot de passe error");
                    $this->view->form = $form;
                }
            } else {

                $values = $form->getValues();
                foreach ($values as $element => $auth_data) {
                    $form->getElement($element)->setValue($auth_data);
                }

                $this->view->info = "alert-error";
                $this->view->infoMessage = $translate->_("Nom ou Mot de passe error");
                $this->view->form = $form;
            }
        } else {

            Zend_Session::destroy();
            $this->view->form = $form;
        }
    }

    public function logoutAction() {
        Zend_Session::destroy();
        $this->_redirect('index');
    }

}