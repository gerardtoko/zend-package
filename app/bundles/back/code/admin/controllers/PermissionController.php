<?php

class Admin_PermissionController extends App_Controller_Action {

    // fait appel a la vue principal
    public function init() {
        App_Ressource::setLinkView();
    }

    public function indexAction() {

        self::pushRessources();

        //Recuperation des informations de la table
        $permission_model = App_Ressource::getModel('admin/permission');
        $role_model = App_Ressource::getModel('admin/role');
        $translate = Zend_Registry::get('translate');

        $roles_rows = $role_model->findAll();
        $conditions = array('group' => 'module', 'order' => 'module ASC');
        $permission_rows = $permission_model->findAll($conditions);

        $array_module = array();
        foreach ($permission_rows as $value) {
            $conditions = null;
            $where = $permission_model->getAdapter()
                    ->quoteInto('module = ?', $value['module']);
            $conditions['where'] = $where;
            $rows = $permission_model->findAll($conditions);
            $array_module[$value['module']] = $rows;
        }

        if (is_null($this->getSession()->getMessage())) {
            $message = $translate->_("les permissions vous permettent de contrôler ce que les utilisateurs peuvent voir et faire sur votre site. Vous pouvez définir un ensemble spécifique de permissions pour chaque groupe.");
            $session = $this->getSession();
            $session->setMessage(array('status' => 'info', 'message' => $message));
        }

        $this->view->role_paginator_ = array_reverse($roles_rows);
        $this->view->messager = $this->getSession()->getMessage();
        $this->view->list = $array_module;
    }

    public function listAction() {
        $this->_forward("index");
    }

    public function addAction() {

        $vals = $this->getRequest()->getPost();
        $permission_relation = App_Ressource::getModel('admin/relation_permission');
        $translate = Zend_Registry::get('translate');

        if (!empty($vals)) {
            $permission_relation->deleteAll();
            foreach ($vals as $val) {
                $array_explode = explode('-', $val);
                $permission_relation->add(array('permission_id' => $array_explode[0], 'group_id' => $array_explode[1]));
            }
        }

        $message = ucfirst($translate->_("the permission has been successfully changed"));
        $message = sprintf('<i class="icon-ok"></i> %s', $message);
        $session = $this->getSession();
        $session->setMessage(array('status' => 'success', 'message' => $message));

        $redBas = Zend_Registry::get("redBase");
        $this->_redirect($redBas);
    }

    public static function pushRessources() {

        if (Zend_Registry::isRegistered('config_app')) {
            $config_app = Zend_Registry::get('config_app');
        } else {
            throw new Exception('app/configs/app.yml no exist');
        }

        $permission_model = App_Ressource::getModel('admin/permission');
        $permission_model->deleteAll();
        $bundle = $config_app['bundles']['all'][1];
        $file = sprintf('%s/shemas/%s.yml', APPLICATION_PATH, $bundle);
        if (file_exists($file)) {
            $config_shema = App_Config::load($file);


            foreach ($config_shema['modules'] as $module => $controllers) {

                if (!is_null($controllers)) {
                    foreach ($controllers as $controller => $actions) {

                        foreach ($actions as $action) {

                            $vals = array(
                                "bundle" => $bundle,
                                "module" => $module,
                                "ressource" => $controller,
                                "action" => $action);

                            $conditions = array();
                            foreach ($vals as $key => $val) {
                                $where = $permission_model->getAdapter()
                                        ->quoteInto(sprintf('%s = ?', $key), $val);
                                $conditions['where'][] = $where;
                            }
                            $rows = $permission_model->findAll($conditions);
                            if (empty($rows)) {
                                $permission_model->add($vals);
                            }
                        }
                    }
                }
            }
        } else {
            throw new Exception("$file no exist");
        }
    }

}