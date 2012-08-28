<?php

/*
 * This file is part of the Factory package.
 *
 * (c) Gerard Toko <gerardtoko@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

Class App_Session {

    protected $sessionNamespace = null;

    /**
     *
     * @param type $session_space
     */
    public function __construct($session_space) {

        $this->sessionNamespace = $session_space;

        if (!Zend_Session::namespaceIsset('app')) {
            $array = array('route', 'message', 'data', 'user');
            foreach ($array as $value) {
                $this->sessionNamespace->$value = null;
            }
        }
    }

    /**
     *
     * @param type $key
     * @param type $value
     * @return type
     */
    public function setMessage(array $value) {
        $this->sessionNamespace->message = $value;
        return $this;
    }

    /**
     *
     * @param type $key
     * @return type
     */
    public function getMessage() {
        if (!empty($this->sessionNamespace->message)) {
            return $this->sessionNamespace->message;
        }
    }

    /**
     *
     * @param type $key
     */
    public function removeMessage() {
        if (!empty($this->sessionNamespace->message)) {
            unset($this->sessionNamespace->message);
        }
    }

    /**
     *
     * @param type $key
     * @param type $value
     * @return type
     */
    public function setUser(array $value) {
        $this->sessionNamespace->user = $value;
        return $this;
    }

    /**
     *
     * @param type $key
     * @return type
     */
    public function getUser() {
        if (!empty($this->sessionNamespace->user)) {
            return $this->sessionNamespace->user;
        }
    }

    /**
     *
     * @param type $key
     */
    public function removeUser() {
        if (!empty($this->sessionNamespace->user)) {
            unset($this->sessionNamespace->user);
        }
    }

    /**
     *
     * @param type $key
     * @param type $value
     * @return type
     */
    public function setData($key, $value) {
        $this->sessionNamespace->data[$key] = $value;
        return $this;
    }

    /**
     *
     * @param type $key
     * @return type
     */
    public function getData($key) {
        $data = $this->sessionNamespace->data;
        if (!empty($data[$key])) {
            return $this->sessionNamespace->data[$key];
        }
    }

    /**
     *
     * @param type $key
     * @return boolean
     */
    public function hasData($key) {
        $data = $this->sessionNamespace->data;
        if (!empty($data[$key])) {
            return true;
        }
    }

    /**
     *
     * @param type $key
     */
    public function removeData($key) {
        $data = $this->sessionNamespace->data;
        if (!empty($data[$key])) {
            unset($this->sessionNamespace->data[$key]);
        }
    }

    public function setLastRequest($value) {
        $this->sessionNamespace->lastRequest = $value;
        return $this;
    }

    public function getLastRequest() {
        return $this->sessionNamespace->lastRequest;
    }

    public function setLastRoute(array $value) {
        $this->sessionNamespace->route = $value;
        return $this;
    }

    public function getLastRoute() {
        return $this->sessionNamespace->route;
    }

}