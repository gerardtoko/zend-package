<?php

/*
 * This file is part of the Factory package.
 *
 * (c) Gerard Toko <gerardtoko@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class App_Route_Html {

    protected $_uri_req;
    protected $_path_view;
    protected $_result = null;

    /**
     *
     * @return string
     * @throws Exception 
     */
    public function match() {

        if (!Zend_Registry::isRegistered('req_uri') || !Zend_Registry::isRegistered('views')) {
            throw new Exception("views and req_uri is not in registry variable");
        }

        $this->_uri_req = Zend_Registry::get('req_uri');
        $this->_path_view = Zend_Registry::get('views')->getScriptPaths();
        $this->_path_view = $this->_path_view[1];

        $splits_req = explode('/', trim($this->_uri_req, '/'));
        $cnt = count($splits_req);

        if (!empty($splits_req) && !empty($splits_req[0])) {

            for ($i = $cnt; $i > 0; $i--) {
                if ($this->_control($splits_req, $i) == true) {

                    $this->setParams(array_slice($splits_req, $i));

                    return $this->_result;
                }
            }
            if (is_null($this->_result)) {
                $this->setParams($splits_req);
                return 'page.phtml';
            }
        } else {

            return 'index.phtml';
        }
    }

    /**
     *
     * @param type $splits_req
     * @param type $i
     * @return boolean 
     */
    public function _control($splits_req, $i) {


        $param = "";
        for ($y = 0; $y < $i; $y++) {
            $param .= "/" . $splits_req[$y];
        }
        $param = trim($param, '/');

        if (file_exists(sprintf('%s%s.phtml', $this->_path_view, $param))) {
            $this->_result = sprintf('%s.phtml', $param);
            return true;
        }

        //get index
        array_pop($splits_req);
        $param = "";
        $i -= 1;
        for ($y = 0; $y < $i; $y++) {
            $param .= "/" . $splits_req[$y];
        }
        $param = trim($param, '/');

        if (file_exists(sprintf('%s%s/index.phtml', $this->_path_view, $param))) {
            $this->_result = sprintf('%s/index.phtml', $param);
            return true;
        }
    }

    /**
     *
     * @param array $array
     * @return null 
     */
    public function setParams(array $array) {

        if (!empty($array)) {

            $cnt = count($array);
            $keys = array();
            $values = array();

            for ($i = 0; $i < $cnt; $i++) {
                if ((0) % 2 == 0) {

                    if ($i % 2 == 0) {

                        //Is even, is key
                        $keys[] = $array[$i];
                    } else {
                        ////Is odd, is value;
                        $values[] = $array[$i];
                    }
                } else {

                    if ($i % 2 != 0) {
                        //Is even, is key
                        $keys[] = $array[$i];
                    } else {
                        ////Is odd, is value;
                        $values[] = $array[$i];
                    }
                }
            }

            // Dans le cas il y a un oublie de spécification d'une values
            if (count($values) < count($keys)) {
                $values[] = " ";
                $parem = array_combine($keys, $values);
            } else {
                $parem = array_combine($keys, $values);
            }
            Zend_Controller_Front::getInstance()->getRequest()->setParams($parem);
            return $parem;
        } else {
            return null;
        }
    }

}