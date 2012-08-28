<?php
/*
 * This file is part of the Factory package.
 *
 * (c) Gerard Toko <gerardtoko@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


ini_set("display_errors", true);

if (version_compare(phpversion(), '5.3.2', '<')===true) {
    include "version.inc";
    exit;
}

#directory app configuration
defined('APPLICATION_PATH')
        || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/app'));

#directory root
defined('DIR_PATH')
        || define('DIR_PATH', realpath(dirname(__FILE__)));


#envoi du dossier lib
set_include_path(DIR_PATH . '/lib');


//Autoloader
require_once 'Zend/Loader/Autoloader.php';
$loader = Zend_Loader_Autoloader::getInstance();
$loader->registerNamespace('App')
        ->registerNamespace('Zend')
        ->registerNamespace('ZendX')
        ->setFallbackAutoloader(true);

$file = APPLICATION_PATH . '/configs/app.yml';
if (file_exists($file)) {
    $config = App_Config::load($file);
    Zend_Registry::set('config_app', $config);
} else {
    throw new Exception("$file no exist");
}

//contruction de url reel
$req_uri_base = !empty($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : "";
$req_uri = str_replace($config['base'], '/', $req_uri_base);


Zend_Registry::set('req_uri', $req_uri);

if (!empty($config['namespaces'])) {
    foreach ($config['namespaces'] as $namespace) {
        $loader->registerNamespace($namespace);
    }
}


$front = Zend_Controller_Front::getInstance();
$front->setBaseUrl($config['base'])
        ->setDefaultModule($config['default_module'])
        ->getRouter()
        ->addRoute(null, new Zend_Controller_Router_Route(':bundle/:module/:controller/:action/*'));

// Add bundles
foreach ($config['bundles']['all'] as $bundle) {
    $directory = APPLICATION_PATH . '/bundles/' . $bundle . '/code';
    if (is_dir($directory)) {
        $front->addModuleDirectory($directory);
    }
}


//Add plugins
foreach ($config['plugins'] as $plugin => $status) {
    if ($status) {
        App_Ressource::loadPlugin($plugin);
    }
}

$plugin = new Zend_Controller_Plugin_ErrorHandler();
$plugin->setErrorHandlerModule('error')
        ->setErrorHandlerController('index')
        ->setErrorHandlerAction('error');
$front->registerPlugin($plugin);

$front->returnResponse(true);
$front->throwExceptions(false);
$response = $front->dispatch();

$eventInstance = App_Event::getInstance();

//On enleve le message
if (Zend_Registry::isRegistered('session_app')) {

    $session = Zend_Registry::get('session_app');
    $message = $session->getMessage();

    $eventInstance->dispatch('response_remove_session_messager', $message);
    $session->removeMessage();
}

$response_event = true;
$eventInstance->dispatch('response_send_view', $response_event);

//Retourne response
$response->sendResponse();


