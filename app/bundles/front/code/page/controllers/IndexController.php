<?php

class IndexController extends App_Controller_Action {

    public function indexAction() {

        if (App_Ressource::moduleIsRegistered('path')) {

            $url = substr(Zend_Registry::get('req_uri'), 1);
            $path_model = App_Ressource::getModel('path/path');
            $row_path = $path_model->findOne($url, "url");

            if (!empty($row_path) || $url == "" || $url = Zend_Controller_Front::getInstance()->getBaseUrl()) {
                $route_html = new App_Route_Html();
                $view_file = $route_html->match();

                $this->dispatchEvent('onPage_index_index_scriptview', &$view_file);
                $this->renderScript($view_file);
            } else {
                Zend_Registry::set('page_no_found', true);
                throw new Exception("page no found");
            }
        } else {

            $route_html = new App_Route_Html();
            $view_file = $route_html->match();

            $this->dispatchEvent('onPage_index_index_scriptview', &$view_file);
            $this->renderScript($view_file);
        }
    }

}