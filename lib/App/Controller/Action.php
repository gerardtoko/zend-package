<?php
/*
 * This file is part of the Factory package.
 *
 * (c) Gerard Toko <gerardtoko@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class App_Controller_Action extends Zend_Controller_Action {

    /**
     *
     * @return type 
     */
    public function getSession() {
        return Zend_Registry::get('session_app');
    }
    
    
    /**
     *
     * @param type $action
     * @param type $event 
     */
    public function dispatchEvent($action, &$event) {
        $eventInstance = App_Event::getInstance();
        $eventInstance->dispatch($action, $event);
    }
}