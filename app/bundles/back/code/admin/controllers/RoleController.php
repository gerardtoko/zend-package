<?php

class Admin_RoleController extends App_Controller_Action {

    // fait appel a la vue principal
    public function init() {
        App_Ressource::setLinkView();
    }

    public function indexAction() {

        //Recupeation des params
        $pAll = $this->_getAllParams();
        $this->view->removeall = sprintf('%s/removeall', $this->view->base);

        // Recuperation de l'url de base
        $redBase = Zend_Registry::get('redBase') . DIRECTORY_SEPARATOR . "index";

        //Recuperation des informations de la table
        $role_model = App_Ressource::getModel('admin/role');
        $cols = $role_model->getInfo("cols");

        $translate = Zend_Registry::get('translate');
        $t_active = ucfirst($translate->_('active'));
        $t_inactive = ucfirst($translate->_('inactive'));

        $session = $this->getSession();

        if (is_null($this->getSession()->getMessage())) {
            $message = $translate->_("les rôles permettent de définir précisément la sécurité et l'administration. Un rôle définit un groupe d'utilisateurs qui ont certains privilèges tels que définis dans la page des permissions. Par exemple :  modérateur, administrateur, etc.");
            $session->setMessage(array('status' => 'info', 'message' => $message));
        }

        //Recuperation des parrams et attribution par default
        $page = (int) ((isset($pAll['page'])) ? $pAll['page'] : 1);
        $sort = (string) ((isset($pAll['sort'])) ? $pAll['sort'] : "asc");
        $order = (string) ((isset($pAll['order'])) ? $pAll['order'] : $cols[0]);
        $limit = (int) ((isset($pAll['limit'])) ? $pAll['limit'] : 30);
        $like = (string) ((isset($pAll['like'])) ? $pAll['like'] : 'null');

        Zend_Registry::set('sort', $sort);

        //Query
        $orderQuery = sprintf("%s %s", $order, strtoupper($sort));

        //Creation de la query where
        $whereQuery = NULL;

        /**
         *
         * PAGINATION
         */
        //Creation de la pagination

        $req = $role_model->select();
        if ($like != "null") {
            App_Cache::clean();
            $req = $role_model->getQuerySearch($like);
        }

        $paginator = App_Ressource::getPaginator($page, $whereQuery, $orderQuery, $limit, $req);

        //Info de la page de pagination
        $pages = $paginator->getPages();

        //PAGINATION
        foreach ($pages->pagesInRange as $index) {
            $paginatorItem[$index]['content'] = $index;
            $paginatorItem[$index]['class'] = ($page == $index) ? "active" : null;
            $paginatorItem[$index]['link'] = $redBase . sprintf("/page/%s/sort/%s/order/%s/limit/%s/like/%s", $index, $sort, $order, $limit, $like);
        }

        // PREVIOUS
        if (isset($pages->previous)) {
            $this->view->paginitionPrev__ = $redBase . sprintf("/page/%s/sort/%s/order/%s/limit/%s/like/%s", $pages->previous, $sort, $order, $limit, $like);
        }

        //NEXT
        if (isset($pages->next)) {
            $this->view->paginitionNext__ = $redBase . sprintf("/page/%s/sort/%s/order/%s/limit/%s/like/%s", $pages->next, $sort, $order, $limit, $like);
        }


        //Onglet
        foreach ($cols as $val) {

            if ($order === $val) {
                if (Zend_Registry::get('sort') == "desc") {

                    $sort = "asc";
                    $arrow = 'icon-arrow-down';
                } else {
                    $sort = "desc";
                    $arrow = 'icon-arrow-up';
                }

                $this->view->icon_arrow__ = array($val, $arrow);
            } else {
                $sort = "asc";
            }

            $rV = $redBase . sprintf("/page/%s/sort/%s/order/%s/limit/%s/like/%s", $page, $sort, $val, $limit, $like);
            $viewVal = $val . "__";
            $this->view->$viewVal = $rV;
        }

        //limitation
        for ($index1 = 1; $index1 < 5; $index1++) {
            $limitElt = $index1 * 30;
            $limitSlip = (string) ("limit" . ($limitElt) . "__");
            $this->view->$limitSlip = $redBase . sprintf("/page/%s/sort/%s/order/%s/limit/%s/like/%s", $page, $sort, $val, $limitElt, $like);
        }

        
        //Render
        $this->view->status_ = array($t_active, $t_inactive);
        $this->view->search_url = $redBase . sprintf("/page/%s/sort/%s/order/%s/limit/%s/like/", $index, $sort, $order, $limit);
        $this->view->refresh_ = $redBase;
        $this->view->page__ = $pages->current;
        $this->view->paginition__ = $paginatorItem;
        $this->view->paginitionMax__ = $pages->lastPageInRange;
        $this->view->limit__ = $pages->itemCountPerPage;
        $this->view->count__ = $pages->totalItemCount;
        $this->view->list = iterator_to_array($paginator);
        $this->view->messager = $session->getMessage();
    }

    public function listAction() {
        $this->_forward("index");
    }

    public function addAction() {

        $form = App_Ressource::getForm('admin/role');
        $this->dispatchEvent('onAdmin_role_add_builder_form', $form);

        $translate = Zend_Registry::get('translate');
        $redBase = Zend_Registry::get("redBase");
        $this->view->refresh_ = $this->view->addLink;
        $session = $this->getSession();

        if ($this->getRequest()->isPost()) {

            if ($form->isValid($_POST)) {

                //Recuperation des values
                $vals = $form->getValues();
                $this->dispatchEvent('onAdmin_role_add_add_database', $vals);

                //Recuperation de la table
                $role_model = App_Ressource::getModel('admin/role');
                $role_model->add($vals);

                $message = vsprintf($translate->_("the %s %s has been successfully added"), array((string) $this->view->nameController,
                    (string) $vals[$role_model->getNameCol(1)]));

                $session->setMessage(array('status' => 'success', 'message' => $message));

                App_Cache::clean();

                //Redirection Url

                $this->_redirect($redBase);
            } else {

                // En cas d'erreur on redirection
                $vals = $form->getValues();
                foreach ($vals as $elt => $val) {
                    $form->getElement($elt)->setValue($val);
                }
                $form->setAction(App_Ressource::getActionForm("action"));
                $this->view->form = $form;
            }
        } else {
            $form->setAction(App_Ressource::getActionForm("action"));
            $this->view->form = $form;
        }
    }

    public function editAction() {

        $id_current = $this->_getParam('id');

        //Creation des roles importants
        $form = App_Ressource::getForm('admin/role'); //Creation des roles importants
        $this->dispatchEvent('onAdmin_role_edit_builder_form', $form);

        $role_model = App_Ressource::getModel('admin/role');
        $field = $role_model->findOne($id_current);
        $key = $role_model->getNameCol(0);

        $session = $this->getSession();

        $this->view->title_ = $field[$role_model->getNameCol(1)];
        $this->view->refresh_ = sprintf('%s%s', $this->view->editLink, $id_current);

        //Element Delete
        $translate = Zend_Registry::get('translate');

        //hidden Element
        $hidden = new Zend_Form_Element_Hidden($role_model->getNameCol(0));
        $hidden->setValue($this->_getParam("id"));
        $hidden->setDecorators(array('ViewHelper'));
        $form->addElement($hidden);

        //Verification de post pour envoye de donne dans la DataBase
        if ($this->getRequest()->isPost()) {

            //Validation formulaire
            if ($form->isValid($_POST)) {

                //Recuperation des donnees
                $vals = $form->getValues();
                $this->dispatchEvent('onAdmin_role_edit_save_database', $vals);

                $conditions = array();
                $conditions[] = sprintf('%s = %s', $key, $vals[$key]);
                $role_model->save($vals, $conditions);

                $message = vsprintf($translate->_("the %s %s has been successfully changed"), array((string) $this->view->nameController,
                    (string) $vals[$role_model->getNameCol(1)]));

                $session->setMessage(array('status' => 'success', 'message' => $message));

                App_Cache::clean();

                //Redirection
                $redBase = Zend_Registry::get("redBase");
                $this->_redirect($redBase);
            } else {
                //Recuperation des donnees
                $vals = $form->getValues();

                // On alimente le formulaire avec les donnees
                foreach ($vals as $elt => $val) {
                    $form->getElement($elt)->setValue($val);
                }

                // Definition de l'action du formulaire
                $form->setAction(App_Ressource::getactionForm("actionId"));
                $this->view->form = $form;
            }
        } else {

            //Alimemtation du formulaire
            foreach ($field as $elt => $val) {
                if ($form->getElement($elt)) {
                    $form->getElement($elt)->setValue($val);
                }
            }

            //Definition de l'action du formulaire
            $form->setAction(App_Ressource::getactionForm("actionId"));

            $this->view->delete_btn = App_Ressource::getActionForm('actionId', 'delete');
            $this->view->form = $form;
        }
    }

    public function deleteAction() {

        $id_current = $this->_getParam('id');
        $role_model = App_Ressource::getModel('admin/role');
        $key = $role_model->getNameCol(0);
        $translate = Zend_Registry::get('translate');

        $role_current = $role_model->findOne($id_current);
        $this->view->title_ = $role_current[$role_model->getNameCol(1)];
        $session = $this->getSession();

        $req_context = Zend_Registry::get('reqContext');
        $file = sprintf('%s/bundles/back/configs/%s/deny_rows.yml', APPLICATION_PATH, $req_context['mod']);

        $id_deny = null;
        if (file_exists($file)) {
            $config_deny = App_Config::load($file);

            if (!empty($config_deny[$key])) {
                $deny_role = $config_deny[$key];
                foreach ($deny_role as $key => $value) {
                    if ((int) $value == (int) $id_current) {
                        $id_deny = $value;
                        break;
                    }
                }
            }
        }

        if ($id_deny != (int) $id_current) {


            if ($this->getRequest()->isPost()) {

                $translate = Zend_Registry::get('translate');
                $val_post = $this->getRequest()->getPost();
                if (array_key_exists(ucfirst($translate->_('confimation')), $val_post)) {

                    $this->dispatchEvent('onAdmin_role_delete', $id_current);
                    $role_model->deleteWithDependency($id_current);
                    App_Cache::clean();

                    $message = vsprintf($translate->_("the %s %s has been successfully removed"), array((string) $this->view->nameController,
                        (string) $role_current[$role_model->getNameCol(1)]));

                    $session = $this->getSession();
                    $session->setMessage(array('status' => 'success', 'message' => $message));
                }

                //Redirection
                $redBas = Zend_Registry::get("redBase");
                $this->_redirect($redBas);
            } else {

                $message = vsprintf($translate->_("delete the %s %s"), array((string) $this->view->nameController,
                    $this->view->title_));
                $session->setMessage(array('status' => 'error', 'message' => $message));

                $form = App_Ressource::getForm('delete');

                //Creation des roles importants
                $form->setAction(App_Ressource::getactionForm('actionId'));
                $hidden = $form->getElement('hidden');
                $hidden->setValue($id_current)
                        ->setName($key);
                $this->view->form = $form;
                $this->view->messager = $this->getSession()->getMessage();
            }
        } else {

            $message = vsprintf($translate->_("%s not remove, it is locked"), array($this->view->title_));
            $session->setMessage(array('status' => 'error', 'message' => $message));

            $redBas = Zend_Registry::get("redBase");
            $this->_redirect($redBas);
        }
    }

    public function removeallAction() {

        $translate = Zend_Registry::get('translate');
        $role_model = App_Ressource::getModel('admin/role');
        $key_model = $role_model->getNameCol(0);
        $req_context = Zend_Registry::get('reqContext');
        $session = $this->getSession();

        if ($_SERVER["REQUEST_METHOD"] == 'POST') {

            $values_post = $this->getRequest()->getPost();

            if (array_key_exists(ucfirst($translate->_('confimation')), $values_post)) {

                unset($values_post[ucfirst($translate->_('confimation'))]);

                $file = sprintf('%s/bundles/back/configs/%s/deny_rows.yml', APPLICATION_PATH, $req_context['mod']);
                if (file_exists($file)) {
                    $config_deny = App_Config::load($file);

                    if (!empty($config_deny[$key_model])) {
                        $deny_user = $config_deny[$key_model];
                        foreach ($values_post as $key_post => $value_post) {
                            if (in_array($value_post, $deny_user)) {
                                $message = vsprintf($translate->_("%s not remove, it is locked"), array(substr($key_post, 0, -1)));
                                $message = sprintf('<i class="icon-remove"></i> %s', $message);
                                $session->setMessage(array('status' => 'error', 'message' => $message));
                                $redBas = Zend_Registry::get("redBase");
                                $this->_redirect($redBas);
                            }
                        }
                    }
                }

                $this->dispatchEvent('onAdmin_role_removeall', $values_post);
                $role_model->deleteAllWithDependency($values_post);

                $message = vsprintf($translate->_("the %ss %s was successfully deleted"), array((string) $this->view->nameController,
                    implode(", ", array_keys($values_post))));
                $session->setMessage(array('status' => 'success', 'message' => $message));
                $this->view->messager = $this->getSession()->getMessage();
            }

            $this->_redirect($this->view->base);
        } else {

            if (empty($_GET))
                return $this->_redirect($this->view->base);

            $form_delete = App_Ressource::getForm('delete');
            $form_delete->setAction(sprintf('%s/removeall', $this->view->base));
            $hidden = $form_delete->getElement('hidden');
            $form_delete->removeElement('hidden');

            foreach ($_GET as $key => $value) {
                $hidden = new Zend_Form_Element_Hidden('hidden');
                $hidden->setDecorators(array('ViewHelper'));
                $hidden->setValue($value)->setName($key);
                $form_delete->addElement($hidden);
            }

            $message = vsprintf($translate->_("removal the %ss %s"), array((string) $this->view->nameController,
                implode(", ", array_keys($_GET))));

            $session->setMessage(array('status' => 'error', 'message' => $message));


            $this->view->form = $form_delete;
            $this->view->messager = $this->getSession()->getMessage();
        }
    }

}