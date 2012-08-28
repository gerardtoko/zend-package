<?php

class DashBord_IndexController extends App_Controller_Action {

    public function indexAction() {
        $eventInstance = App_Event::getInstance();
        $array_content = array();
        $eventInstance->dispatch('onDashbord_index_index_sendView', $array_content);
    }

}