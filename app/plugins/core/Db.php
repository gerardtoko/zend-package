<?php

/*
 * This file is part of the Factory package.
 *
 * (c) Gerard Toko <gerardtoko@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class Plugin_Db extends Zend_Controller_Plugin_Abstract {

    
    /**
     *
     * @param Zend_Controller_Request_Abstract $request
     * @return null 
     */
    public function dispatchLoopStartup(Zend_Controller_Request_Abstract $request) {

        $file = APPLICATION_PATH . '/configs/database.yml';
        if (file_exists($file)) {

            $config = App_Config::load($file);
            $config_auth = $config['auth'];
            $config_db = $config['database'];

            //db
            try {
                $db = Zend_Db::factory($config_db['adapter'], $config_db['params']);
                $db->getConnection();
                Zend_Db_Table::setDefaultAdapter($db);
            } catch (Exception $exc) {
                $db = null;
            }

            //authentification
            if (!is_null($db)) {
                $auth_adapter = new Zend_Auth_Adapter_DbTable($db);
                $auth_adapter->setTableName($config_auth['table_name'])
                        ->setIdentityColumn($config_auth['identity_column'])
                        ->setCredentialColumn($config_auth['credential_column'])
                        ->setCredentialTreatment($config_auth['crendtial_treatment']);


                Zend_Registry::set('auth', $auth_adapter);
                Zend_Registry::set("db", $db);

                return $db;
            }
        }
    }
}
