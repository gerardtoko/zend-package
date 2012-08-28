<?php

/*
 * This file is part of the Factory package.
 *
 * (c) Gerard Toko <gerardtoko@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

Class App_Ressource {

    /**
     *
     * @param type $name
     * @return \model
     */
    public static function getShema($name = null, $new_instance = false) {
        $class_name = App_Ressource::getRessource('Model_Shema', $name);
        return App_Ressource::storageControl($class_name, $new_instance);
    }

    /**
     *
     * @param type $name
     * @return \model
     */
    public static function getModel($name = null, $new_instance = false) {

        $class_name = App_Ressource::getRessource('Model_DbTable', $name);

        return App_Ressource::storageControl($class_name, $new_instance);
    }

    /**
     *
     * @param type $name
     * @throws Exception 
     */
    public static function loadPlugin($name) {
        $explode = explode('/', $name);
        $name = sprintf('%s/%s', $explode[0], ucfirst($explode[1]));
        $file = (string) (APPLICATION_PATH . '/plugins/' . $name . '.php');
        if (file_exists($file)) {
            require $file;
            $class = 'Plugin_' . $explode[1];
            $plugin = new $class;
            $front = Zend_Controller_Front::getInstance();
            $front->registerPlugin($plugin);
        } else {
            throw new Exception('no plugin file exist');
        }
    }

    /**
     *
     * @param type $name
     * @return \collection
     */
    public static function getCollection($name = null, $new_instance = false) {
        $class_name = App_Ressource::getRessource('Model_Collection', $name);
        return App_Ressource::storageControl($class_name, $new_instance);
    }

    /**
     *
     * @param type $name
     * @param type $new_instance
     * @return type 
     */
    public static function getEvent($name = null, $new_instance = false) {
        $class_name = App_Ressource::getRessource('Event', $name);
        return App_Ressource::storageControl($class_name, $new_instance);
    }

    /**
     *
     * @param type $class_name
     * @param type $new_instance
     * @return \class_name 
     */
    private static function storageControl($class_name, $new_instance) {

        if (Zend_Registry::isRegistered('app_ressource_storage')) {

            $storage_ressource = Zend_Registry::get('app_ressource_storage');

            if (!empty($storage_ressource[$class_name]) || $new_instance = false) {
                $class = $storage_ressource[$class_name];
            } else {
                $class = new $class_name;
                $storage_ressource[$class_name] = $class;
                Zend_Registry::set('app_ressource_storage', $storage_ressource);
            }
        } else {

            $class = new $class_name;
            $storage_ressource = array($class_name);
            Zend_Registry::set('app_ressource_storage', $storage_ressource);
        }

        return $class;
    }

    /**
     *
     * @param type $name
     * @return \model
     */
    public static function getForm($name = null, $new_instance = false) {
        $class_name = App_Ressource::getRessource('Form', $name);
        return App_Ressource::storageControl($class_name, $new_instance);
    }

    /**
     *
     * @param type $name
     * @return \model
     */
    public static function getBuilderForm($name = null, $new_instance = false) {
        $class_name = App_Ressource::getRessource('Form_Builder', $name);
        $builder_form = App_Ressource::storageControl($class_name, $new_instance);
        return $builder_form::render();
    }

    /*
     * @description
     * @author gerard toko
     * @param type $curentPage
     * @param type $where
     * @param type $order
     * @param type $limit
     * @return \Zend_Paginator
     */

    public static function getPaginator($curent_page = 1, $where = null, $order = null, $limit = null, $join = null) {

        if (is_null($join)) {
            $req = self::getModel()->select();
        } else {
            $req = $join;
        }

        $req = !is_null($where) || is_string($where) ? $req->where($where) : $req;
        $req = !is_null($order) || is_string($order) ? $req->order($order) : $req;
        $limit_page = !is_null($limit) || is_string($limit) ? $limit : 30;

        //Adaptateur paginator
        $adapter = new Zend_Paginator_Adapter_DbSelect($req);
        $paginator = new Zend_Paginator($adapter);
        $paginator->setItemCountPerPage(9)
                ->setPageRange(9)
                ->setCurrentPageNumber($curent_page)
                ->setItemCountPerPage($limit_page);

        //Type Scrolling
        Zend_Paginator::setDefaultScrollingStyle('Sliding');
        return $paginator;
    }

    /**
     *
     * @param type $value
     * @param type $act_param
     * @param type $con
     * @param type $mod
     * @return string
     */
    public static function getActionForm($value, $act_param = null, $con = null, $mod = null) {

        $req = Zend_Registry::get('reqContext');
        $mod = is_null($mod) ? $req['mod'] : $mod;
        $con = is_null($con) ? $req['con'] : $con;
        $act = is_null($act_param) ? $req['act'] : $act_param;

        $action = sprintf('%s/%s/%s/%s/%s', $req['bas'], $req['bun'], $mod, $con, $act);

        switch ($value) {
            case "action":
                return $action;
                break;

            case "actionId":
                $actionId =
                        sprintf('%s/id/%s', $action, Zend_Controller_Front::getInstance()->getRequest()->getParam('id'));
                return $actionId;
                break;
        }
    }

    /**
     *
     * @param type $name
     * @return type
     */
    public static function getNamespace($name) {
        if (preg_match('#\_#', $name)) {
            $array_name = explode('_', $name);
            $name = null;
            foreach ($array_name as $value) {
                $name .= ucfirst($value) . '_';
            }
            $name = substr($name, 0, -1);
        } else {
            $name = ucfirst($name);
        }

        return $name;
    }

    /**
     *
     * @param type $space
     * @param type $name
     * @return type
     */
    public static function getRessource($space, $name) {


        $space = sprintf('_%s_', ucfirst($space));


        $req = Zend_Registry::isRegistered('reqContext') ? Zend_Registry::get('reqContext') : null;


        if (preg_match('#\/#', $name)) {

            $array = array();
            $array = explode('/', $name);
            $name = App_Ressource::getNamespace($array[1]);
            $ressource = ucfirst($array[0]) . $space . $name;

            if (!self::moduleIsRegistered($array[0])) {
                throw new Exception(sprintf("module %s not active or present", $array[0]));
            }
        } else {
            $mod = isset($req['mod']) ? $req['mod'] : "default";
            $ressource = ucfirst($mod) . $space;
            $name = empty($name) ? $req['con'] : $name;
            $name = App_Ressource::getNamespace($name);
            $ressource = is_null($name) ? $ressource . ucfirst($req['con']) : $ressource . $name;

            if (!self::moduleIsRegistered($mod)) {
                throw new Exception(sprintf("module %s not active or present", $mod));
            }
        }

        return $ressource;
    }

    /**
     * 
     */
    public static function setLinkView() {
        $view = Zend_Registry::get('views');
        $view->addLink = (string) ($view->base . "/add");
        $view->editLink = (string) ($view->base . "/edit/id/");
        $view->deleteLink = (string) ($view->base . "/delete/id/");
    }

    /**
     *
     * @param type $sub 
     */
    public static function setSubLinkView($sub) {
        $view = Zend_Registry::get('views');
        App_Ressource::setLinkView();
        $view->subAction = $sub;
        $req = Zend_Registry::get('reqContext');
        $bun = $req['bun'];
        $base = $req['bas'];
        $mod = $req['mod'];
        $con = $req['con'];

        //Add subLink

        $view->subAddLink = sprintf('%s/%s/%s/%s/add/%s/', $base, $bun, $mod, $view->subAction, $con);
        $view->subListLink = sprintf('%s/%s/%s/%s/list/%s/', $base, $bun, $mod, $view->subAction, $con);
    }

    /**
     *
     * @param type $module
     * @return boolean
     * @throws Exception 
     */
    public static function moduleIsRegistered($module) {

        if (Zend_Registry::isRegistered('config_app')) {
            $config_app = Zend_Registry::get('config_app');
        } else {
            throw new Exception('app/configs/app.yml no exist');
        }

        foreach ($config_app['bundles']['all'] as $bundle) {

            $file = sprintf('%s/shemas/%s.yml', APPLICATION_PATH, $bundle);
            if (file_exists($file)) {
                $config_shema = App_Config::load($file);

                if (array_key_exists($module, $config_shema['modules'])) {
                    $flag_exist = true;
                    $file = sprintf('%s/bundles/%s/configs/%s/info.yml', APPLICATION_PATH, $bundle, $module);
                    if (file_exists($file)) {
                        $info = App_Config::load($file);
                        if ($info['active'] == true) {
                            return true;
                        }
                    } else {
                        return false;
                    }
                } 
            } else {
                throw new Exception("$file no exist");
            }
        }

    }

}