<?php

/*
 * This file is part of the Factory package.
 *
 * (c) Gerard Toko <gerardtoko@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class Plugin_Template extends Zend_Controller_Plugin_Abstract {

    /**
     *
     * @param Zend_Controller_Request_Abstract $request
     * @return \Zend_View
     * @throws Exception 
     */
    public function dispatchLoopStartup(Zend_Controller_Request_Abstract $request) {

        $config_app = Zend_Registry::get('config_app');
        $base_url = Zend_Controller_Front::getInstance()->getBaseUrl();
        $module = $request->getModuleName();
        $bundle = !is_null($this->getRequest()->getParam('bundle')) ?
                $this->getRequest()->getParam('bundle') : $config_app['bundles']['default']['bundle'];

        $file = sprintf('%s/bundles/%s/etc/template.yml', APPLICATION_PATH, $bundle);
        if (file_exists($file)) {

            $config_template = App_Config::load($file);
            $options = array('layout' => 'layout',
                'layoutPath' => sprintf('%s/bundles/%s/template/%s/layouts', APPLICATION_PATH, $bundle, $config_template['current']),
                'contentKey' => 'content');

            $layout = Zend_Layout::startMvc($options);
            $view = new Zend_View();

            $view->addHelperPath(sprintf('%s/lib/App/Helper', DIR_PATH), 'App_Helper');
            $view->addHelperPath(sprintf('%s/bundles/%s/template/%s/helpers', APPLICATION_PATH, $bundle, $config_template['default']), "Template_Helper");
            $view->addHelperPath(sprintf('%s/bundles/%s/template/%s/helpers', APPLICATION_PATH, $bundle, $config_template['current']), "Template_Helper");

            #favicon
            $file = sprintf('%s/web/favicon.ico', DIR_PATH);
            if (file_exists($file)) {
                $view->minifyHeadLink(array('rel' => 'shortcut icon', 'href' => sprintf('%s/web/favicon.ico', $base_url), 'type' => 'image/x-icon'), 'PREPEND');
                $view->minifyHeadLink(array('rel' => 'icon', 'href' => sprintf('%s/web/favicon.ico', $base_url), 'type' => 'image/x-icon'), 'PREPEND');
            }




            $file = sprintf('%s/bundles/%s/etc/head/%s.yml', APPLICATION_PATH, $bundle, $config_template['current']);
            $file = !file_exists($file) ? sprintf('%s/bundles/%s/etc/head/%s.yml', APPLICATION_PATH, $bundle, $config_template['default']) : $file;

            if (file_exists($file)) {

                $config_bundle_view = App_Config::load($file);

                if (!empty($config_bundle_view['doctype'])) {
                    $view->doctype($config_bundle_view['doctype']);
                }


                //meta
                if (!empty($config_bundle_view['meta'])) {
                    foreach ($config_bundle_view['meta'] as $value) {
                        $key_meta = array_keys($value);
                        $value_meta = array_values($value);
                        $view->headMeta($value_meta[1], $value_meta[0], $key_meta[0]);
                    }
                }

                //stylesheets
                if (!empty($config_bundle_view['stylesheets'])) {
                    foreach ($config_bundle_view['stylesheets'] as $stylesheet) {
                        $view->minifyHeadLink()->appendStylesheet(sprintf('%s/web/skins/%s/default/css/%s', $base_url, $bundle, $stylesheet));
                    }
                }


                //scripts
                if (!empty($config_bundle_view['scripts'])) {
                    foreach ($config_bundle_view['scripts'] as $script) {
                        $view->minifyHeadScript()->appendFile(sprintf('%s/web/skins/%s/default/js/%s', $base_url, $bundle, $script));
                    }
                }
            }

            //view ressource
            $file = sprintf('%s/bundles/%s/configs/%s/view.yml', APPLICATION_PATH, $bundle, $module);
            if (file_exists($file)) {

                $config_bundle_view = App_Config::load($file);

                //stylesheets
                if (!empty($config_bundle_view['stylesheets'])) {
                    foreach ($config_bundle_view['stylesheets'] as $stylesheet) {
                        $view->minifyHeadLink()->appendStylesheet(sprintf('%s/web/ressources/%s/css/%s', $base_url, $module, $stylesheet));
                    }
                }

                //scripts
                if (!empty($config_bundle_view['scripts'])) {
                    foreach ($config_bundle_view['scripts'] as $script) {
                        $view->minifyHeadScript()->appendFile(sprintf('%s/web/ressources/%s/js/%s', $base_url, $module, $script));
                    }
                }
            }

            //script partiel
            $view->addScriptPath(sprintf('%s/bundles/%s/template/%s/layouts', APPLICATION_PATH, $bundle, $config_template['default']));
            $view->addScriptPath(sprintf('%s/bundles/%s/template/%s/layouts', APPLICATION_PATH, $bundle, $config_template['current']));

            //script view
            $view->addScriptPath(sprintf('%s/bundles/%s/template/%s/scripts/%s', APPLICATION_PATH, $bundle, $config_template['default'], $module));
            $view->addScriptPath(sprintf('%s/bundles/%s/template/%s/scripts/%s', APPLICATION_PATH, $bundle, $config_template['current'], $module));


            // Add it to the ViewRenderer
            $view_renderer = Zend_Controller_Action_HelperBroker::getStaticHelper('ViewRenderer');
            $view_renderer->setView($view);

            //Stockage dans le registre
            Zend_Registry::set("views", $view);
            Zend_Registry::set('layout', $layout);

            // On retourne l'objet views
            return $view;
        } else {
            throw new Exception($file . 'no exist');
        }
    }

}