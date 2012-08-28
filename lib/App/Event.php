<?php

/*
 * This file is part of the Factory package.
 *
 * (c) Gerard Toko <gerardtoko@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class App_Event {

    protected $_notifs = array();
    private static $_instance = null;

    /**
     *
     * @return App_Event
     */
    public function getInstance() {
        if (self::$_instance == null) {
            self::$_instance = new self;
        }
        return self::$_instance;
    }

    /**
     *
     * @return \App_Event 
     */
    public function resetInstance() {
        self::$_instance = null;
        return $this;
    }

    /**
     *
     * @param type $notifName
     * @return \App_Event 
     */
    public function setNotif($notifName) {
        $this->_notifs[$notifName] = null;
        return $this;
    }

    /**
     *
     * @param type $notifName
     * @return type
     */
    public function getNotif($notifName) {
        if (isset($this->_notifs[$notifName])) {
            return $this->_notifs[$notifName];
        }
    }

    /**
     *
     * @return type
     */
    public function getNotifs() {
        return $this->_notifs;
    }

    /**
     *
     * @param type $notif
     * @return boolean
     */
    public function hasNotif($notifName) {
        if (isset($this->_notifs[$notifName])) {
            return true;
        }
    }

    /**
     *
     * @param type $notifName
     * @return \App_Event 
     */
    public function removeNotif($notifName) {
        unset($this->_notifs[$notifName]);
        return $this;
    }

    /**
     *
     * @param array $listener
     * @param type $notifName
     * @return \App_Event 
     */
    public function addListener(array $listener, $notifName) {
        $this->_notifs[$notifName][] = $listener;
        return $this;
    }

    /**
     *
     * @param array $listener
     * @param type $notifName
     * @return type
     */
    public function getListener(array $listener, $notifName) {
        if (!empty($this->_notifs[$notifName])) {
            $notif = $this->_notifs[$notifName];
            foreach ($notif as $value) {
                if (($listener[0] instanceof $value[0]) && ($listener[1] == $value[1])) {
                    return $value;
                }
            }
        }
    }

    /**
     *
     * @param type $notif
     * @return type
     */
    public function getListeners($notifName) {
        return $this->getnotif($notifName);
    }

    /**
     *
     * @param array $listener
     * @param type $notifName
     * @return boolean
     */
    public function hasListener(array $listener, $notifName) {
        if (!empty($this->_notifs[$notifName])) {
            $notif = $this->_notifs[$notifName];
            foreach ($notif as $value) {
                if (($value[0] instanceof $listener[0]) && ($listener[1] == $value[1])) {
                    return true;
                }
            }
        }
    }

    /**
     *
     * @param array $listener
     * @param type $notifName
     */
    public function removeListener(array $listener, $notifName) {
        if (!empty($this->_notifs[$notifName])) {
            $notif = $this->_notifs[$notifName];
            foreach ($notif as $key => $value) {
                if (($value[0] instanceof $listener[0]) && ($listener[1] == $value[1])) {
                    unset($this->_notifs[$notifName][$key]);
                }
            }
        }
    }

    /**
     *
     * @param type $notif
     * @param type $event
     * @return \App_Event|boolean 
     */
    public function dispatch($notif, &$event) {
        if (!empty($this->_notifs[$notif])) {
            App_Event_Dispatcher::dispatch($this->_notifs[$notif], $event);
            return $this;
        } else {
            return false;
        }
    }

    /**
     *
     * @return \App_Event 
     */
    public function stopDispatch() {
        App_Event_Dispatcher::stop();
        return $this;
    }

}