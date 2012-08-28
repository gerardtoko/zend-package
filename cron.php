<?php

/*
 * This file is part of the Factory package.
 *
 * (c) Gerard Toko <gerardtoko@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Exemple:
 * Dans le fichier crontab de votre serveur linux ajouté cette ligne en fonction de vos besoins:
 *
 * *\/5 * * * * root /path/to/factory/app/cron.sh  newsletter
 *
 * Le message envoyé sera
 * $eventInstance->dispatch('onCrontab_newsletter', $message);
 *
 * Maintenant les listenersEvent qui ecoutent sur onCrontab_newsletter se déclenchent 
 * quand le message est envoyé suite à l'excution du cron tâche
 */
#directory app configuration
defined('APPLICATION_PATH')
        || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/app'));

#directory root
defined('DIR_PATH')
        || define('DIR_PATH', realpath(dirname(__FILE__) . '/'));


#envoi du dossier lib
set_include_path('.'
        . PATH_SEPARATOR . './lib'
        . PATH_SEPARATOR . get_include_path());


//Autoloader
require_once 'Zend/Loader/Autoloader.php';

$loader = Zend_Loader_Autoloader::getInstance();
$loader->registerNamespace('App')
        ->registerNamespace('Zend')
        ->setFallbackAutoloader(true);

#configuration app
$file = APPLICATION_PATH . '/configs/app.yml';
if (file_exists($file)) {
    $config = App_Config::load($file);
    Zend_Registry::set('config_app', $config);
} else {
    throw new Exception("$file no exist");
}

#namespace lib
if (!empty($config['namespaces'])) {
    foreach ($config['namespaces'] as $namespace) {
        $loader->registerNamespace($namespace);
    }
}

#app load ressources
App_Loader_Ressource::pushRessources();


#configuration db
$file = APPLICATION_PATH . '/configs/database.yml';
if (file_exists($file)) {

    $config = App_Config::load($file);
    $config_db = $config['database'];

    try {
        $db = Zend_Db::factory($config_db['adapter'], $config_db['params']);
        $db->getConnection();
        Zend_Db_Table::setDefaultAdapter($db);
        Zend_Registry::set("db", $db);
    } catch (Exception $exc) {
        $db = null;
    }
}


if (!empty($argv[1]) && !empty($argv[2])) {
    #lancement cron tache
    $message = $argv[2];
    $eventInstance = App_Event::getInstance();

    $eventInstance->dispatch(sprintf("onEvent_%s", strtolower($argv[1])), $message);
}
