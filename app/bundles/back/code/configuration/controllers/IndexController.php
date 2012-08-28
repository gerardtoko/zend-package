<?php

class Configuration_IndexController extends App_Controller_Action {

    public function indexAction() {
        $array_content = array();
        $this->dispatchEvent('onConfiguration_index_index_sendView', $array_content);
        $this->view->list = $array_content;
    }
}