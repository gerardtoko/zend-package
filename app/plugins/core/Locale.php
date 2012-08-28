<?php

/*
 * This file is part of the Factory package.
 *
 * (c) Gerard Toko <gerardtoko@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class Plugin_Locale extends Zend_Controller_Plugin_Abstract {

    /**
     *
     * @param Zend_Controller_Request_Abstract $request
     * @throws Exception 
     */
    public function dispatchLoopStartup(Zend_Controller_Request_Abstract $request) {

        //ressources
        $config_app = Zend_Registry::get('config_app');
                
        if ($config_app['app_log'] == true) {
            $file = DIR_PATH . '/var/logs/translate.log';
            if (!file_exists($file)) {
                $file = fopen($file, "w");
                fwrite($file, null);
            }
            $redacteur = new Zend_Log_Writer_Stream($file);
            $log = new Zend_Log($redacteur);
        } else {
            $log = null;
        }


        $bundles = $config_app['bundles']['all'];
        $translate = null;

        try {
            $locale = new Zend_Locale('auto');
        } catch (Zend_Locale_Exception $e) {
            $locale = new Zend_Locale($config_app['locale_default']);
        }


        foreach ($bundles as $bundle) {

            $file = sprintf('%s/shemas/%s.yml', APPLICATION_PATH, $bundle);
            if (file_exists($file)) {
                $config_shema = App_Config::load($file);

                foreach ($config_shema['modules'] as $module => $controllers) {

                    $file = sprintf('%s/bundles/%s/locale/%s/translate/%s.csv', APPLICATION_PATH, $bundle, $module, $locale);
                    $file = !file_exists($file) ? sprintf('%s/bundles/%s/locale/%s/translate/%s.csv', APPLICATION_PATH, $bundle, $module, $config_app['locale_default']) : $file;

                    if (file_exists($file)) {
                        $this->setTranslate($translate, $file, $locale, $log);
                    }
                }
            } else {
                throw new Exception("$file no exist");
            }


            $file = sprintf('%s/bundles/%s/locale/app_form/%s.csv', APPLICATION_PATH, $bundle, $locale);
            $file = !file_exists($file) ? sprintf('%s/bundles/%s/locale/app_form/%s.csv', APPLICATION_PATH, $bundle, $config_app['locale_default']) : $file;

            if (file_exists($file)) {
                $this->setTranslate($translate, $file, $locale, $log);
            }

            Zend_Registry::set("translate", $translate);
            Zend_Registry::set('Zend_Translate', $translate);
        }

        $bundle_current = $request->getParam('bundle');
        $module_current = $request->getModuleName();

        $file = sprintf('%s/bundles/%s/locale/%s/translate/%s.csv', APPLICATION_PATH, $bundle_current, $module_current, $locale);
        $file = !file_exists($file) ? sprintf('%s/bundles/%s/locale/%s/translate/%s.csv', APPLICATION_PATH, $bundle_current, $module_current, $config_app['locale_default']) : $file;

        if (file_exists($file)) {
            $this->setTranslate($translate, $file, $locale, $log);
        }
    }

    /**
     *
     * @param Zend_Translate $translate
     * @param type $file
     * @param type $locale
     * @param type $log 
     */
    public function setTranslate(&$translate, $file, $locale, $log) {
        if (is_null($translate)) {
            $translate = new Zend_Translate(array(
                        'adapter' => 'csv',
                        'content' => $file,
                        'locale' => $locale,
                        'delimiter' => ';',
                        'log' => $log,
                        'logMessage' => "Missing '%message%' within locale '%locale%'",
                        'logPriority' => Zend_Log::ALERT,
                        'logUntranslated' => true
                    ));
        } else {
            $translate->addTranslation(
                    array(
                        'adapter' => 'csv',
                        'content' => $file,
                        'locale' => $locale,
                        'delimiter' => ';',
                        'log' => $log,
                        'logMessage' => "Missing '%message%' within locale '%locale%'",
                        'logPriority' => Zend_Log::ALERT,
                        'logUntranslated' => true
            ));
        }
    }

}