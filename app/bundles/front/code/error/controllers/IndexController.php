<?php

class Error_IndexController extends App_Controller_Action {

    const ENV_PRODUCTION = 'prod';
    const ENV_DEVLOPMENT = 'dev';
    const ENV_TESTING = 'test';
    
    public function errorAction() {

        $config_app = Zend_Registry::get('config_app');
        $base_url = Zend_Controller_Front::getInstance()->getBaseUrl();
        $module = $this->getRequest()->getModuleName();
        $bundle = $config_app['bundles']['default']['bundle'];

      
        $file = sprintf('%s/bundles/%s/etc/template.yml', APPLICATION_PATH, $bundle);
        if (file_exists($file)) {
            $config_template = App_Config::load($file);
            
            if (Zend_Registry::isRegistered('layout')) {
                $layout = Zend_Registry::get('layout');
                $layout->setLayoutPath(sprintf('%s/bundles/%s/template/%s/layouts', APPLICATION_PATH, $bundle, $config_template['current']));
                $layout->setLayout($config_app['error']['layout404']);
            }

            $this->view->addScriptPath(sprintf('%s/bundles/%s/template/%s/scripts/%s', APPLICATION_PATH, $bundle, $config_template['default'], $module));
            $this->view->addScriptPath(sprintf('%s/bundles/%s/template/%s/scripts/%s', APPLICATION_PATH, $bundle, $config_template['current'], $module));
        } else {
            throw new Exception("$file no exist");
        }

        $file = sprintf('%s/configs/env.yml', APPLICATION_PATH);
        if (file_exists($file)) {
           $config_env = App_Config::load($file);
           $env = $config_env['env'];
        }else{
            $config_env = self::ENV_PRODUCTION;
        }
        
        $errors = $this->_getParam('error_handler');

        if (Zend_Registry::isRegistered('page_no_found')) {
            $page_no_found = Zend_Registry::get('page_no_found');
            if($page_no_found == true){
               $errors->type = Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER; 
            }
        }

        switch ($errors->type) {     
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ROUTE:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:

                $this->dispatchEvent('onPage_error_error_dispatch404', &$errors);
                $this->getResponse()->setRawHeader('HTTP/1.1 404 Not Found');
                $this->renderScript('404.phtml');
                break;
            default:

                $exception = $errors->exception;
                $this->dispatchEvent('onPage_error_error_dispatchError', &$errors);

                if ($env == self::ENV_TESTING || $env == self::ENV_DEVLOPMENT) {
                    echo "<pre>";
                    echo $exception->getMessage() . "\n" . $exception->getTraceAsString();
                    echo "</pre>";
                    exit;
                } else {
                    $redacteur = new Zend_Log_Writer_Stream(DIR_PATH . '/data/logs/prod.log');
                    $logger = new Zend_Log($redacteur);
                    $error = $exception->getMessage() . "\n" . $exception->getTraceAsString();
                    $logger->log($error, Zend_Log::ERR);
                    $this->renderScript('error.phtml');
                }

                break;
        }
    }

    public function maintenanceAction() {
        $layout = Zend_Registry::get('layout');
        $layout->resetMvcInstance();
        $this->getResponse()->setRawHeader('HTTP/1.1 503');
        $this->renderScript('maintenance.phtml');
    }

}