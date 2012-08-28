<?php

/*
 * This file is part of the Factory package.
 *
 * (c) Gerard Toko <gerardtoko@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class App_Loader_Ressource {

    /**
     *
     * @throws Exception 
     */
    public static function pushRessources() {

        $autoLoader = Zend_Loader_AutoLoader::getInstance();

        if (Zend_Registry::isRegistered('config_app')) {
            $config_app = Zend_Registry::get('config_app');
        } else {
            throw new Exception('app/configs/app.yml no exist');
        }
        $eventInstance = App_Event::getInstance();


        /**
         *  On load Les Ressources 
         */
        foreach ($config_app['bundles']['all'] as $bundle) {

            $file = sprintf('%s/shemas/%s.yml', APPLICATION_PATH, $bundle);
            if (file_exists($file)) {

                $config_shema = App_Config::load($file);
                foreach ($config_shema['modules'] as $module => $controllers) {

                    if (self::isActiveModule($bundle, $module)) {
                        $ressourceLoader = new Zend_Loader_Autoloader_Resource(array(
                                    'basePath' => sprintf('%s/bundles/%s/code/%s', APPLICATION_PATH, $bundle, $module),
                                    'namespace' => ucfirst($module),
                                ));

                        $ressourceLoader->addResourceType('acl', 'acls/', 'Acl')
                                ->addResourceType('form', 'forms/', 'Form')
                                ->addResourceType('model', 'models/', 'Model')
                                ->addResourceType('event', 'events/', 'Event')
                                ->addResourceType('service', 'services/', 'Service');
                        $autoLoader->pushAutoloader($ressourceLoader);
                    }
                }
            } else {
                throw new Exception("$file no exist");
            }
        }


        /**
         *
         * On load les events
         *  
         */
        foreach ($config_app['bundles']['all'] as $bundle) {
            $file = sprintf('%s/shemas/%s.yml', APPLICATION_PATH, $bundle);
            if (file_exists($file)) {
                $config_shema = App_Config::load($file);
                foreach ($config_shema['modules'] as $module => $controllers) {

                    if (self::isActiveModule($bundle, $module)) {
                        $file = sprintf('%s/bundles/%s/configs/%s/event.yml', APPLICATION_PATH, $bundle, $module);
                        if (file_exists($file)) {

                            $events = App_Config::load($file);

                            if (!empty($events)) {

                                foreach ($events as $event => $ressource) {

                                    if (preg_match("#^(namespaceEvent_)#", $event)) {
                                        $event_namespace = $ressource;
                                        foreach ($event_namespace as $event => $ressource) {
                                            $type = $ressource[2];
                                            unset($ressource[2]);
                                            if ($type == 'instance') {
                                                $ressource[0] = App_Ressource::getEvent($ressource[0]);
                                                $eventInstance->addListener($ressource, $event);
                                            } elseif ($type == 'static') {
                                                $eventInstance->addListener($ressource, $event);
                                            }
                                        }
                                    } else {
                                        $type = $ressource[2];
                                        unset($ressource[2]);
                                        if ($type == 'instance') {
                                            $ressource[0] = App_Ressource::getEvent($ressource[0]);
                                            $eventInstance->addListener($ressource, $event);
                                        } elseif ($type == 'static') {
                                            $eventInstance->addListener($ressource, $event);
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            } else {
                throw new Exception("$file no exist");
            }
        }
    }

    public static function isActiveModule($bundle, $module) {
        $file = sprintf('%s/bundles/%s/configs/%s/info.yml', APPLICATION_PATH, $bundle, $module);
        if (file_exists($file)) {
            $info = App_Config::load($file);
            if ($info['active'] == true) {
                return true;
            }
        } else {
            return true;
        }
    }

}