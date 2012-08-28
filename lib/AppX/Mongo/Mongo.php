<?php

class Plugin_Mongo extends Zend_Controller_Plugin_Abstract {

    public function dispatchLoopStartup(Zend_Controller_Request_Abstract $request) {

        $file = APPLICATION_PATH . '/configs/mongo.yml';
        if (file_exists($file)) {
            
            $config = App_Config::load($file);
            $config_default = $config['default'];

            $dbname = $config_default['dbname'];
            $username = $config_default['params']['username'];
            $password = $config_default['params']['password'];
            $hostname = $config_default['params']['hostname'];

            $con = new Mongo(sprintf("mongodb://%s:%s@%s", $username, $password, $hostname));
            $db_mongo = $con->selectDB($dbname);

            Zend_Registry::set("db_mongo", $db_mongo);
            Zend_Registry::set("mongo_con", $con);
            return $db_mongo;
        }else{
            throw new Exception("$file no exist");
        }
    }

    public function dispatchLoopShutdown() {
        if (Zend_Registry::isRegistered("mongo_con")) {
            $con = Zend_Registry::get("mongo_con");
            $con->close();
        }
    }

}