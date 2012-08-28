<?php

/*
 * This file is part of the Factory package.
 *
 * (c) Gerard Toko <gerardtoko@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
class Plugin_Session extends Zend_Controller_Plugin_Abstract {

    // namespace session
    protected $sessionNamespace;
    // session de verouillage
    protected $sessionLock;

    /**
     *
     * @param Zend_Controller_Request_Abstract $request
     * @return type 
     */
    public function dispatchLoopStartup(Zend_Controller_Request_Abstract $request) {

        $file = APPLICATION_PATH . '/configs/session.yml';

        if (file_exists($file)) {
            $config = App_Config::load($file);
            $config = $config['sessions'];
            
            $directory = DIR_PATH . '/var/session';        
            if(is_dir($directory)){
                 $config['options']['save_path'] = $directory;
            }else{
                mkdir($directory, 0775, true);
                $config['options']['save_path'] = $directory;
            }

            Zend_Session::setOptions($config['options']);

            // demarrage d'une session
            Zend_Session::start();

            $this->sessionNamespace = new Zend_Session_Namespace('app', true);

            $app_session = new App_Session($this->sessionNamespace);

            // deverrouillage si deja verrouille
            if ($this->sessionNamespace->isLocked()) {
                $this->sessionNamespace->unLock();
            }

            // empecher les vols de sessions
            if (!isset($this->sessionNamespace->initialized)) {
                Zend_Session::regenerateId();
                $this->sessionNamespace->initialized = true;
            }

            // sauvegarde de la session dans le registre
            Zend_Registry::set('session_app', $app_session);
            Zend_Registry::set('session', $this->sessionNamespace);
            return $this->sessionNamespace;
        }
    }

    /**
     * 
     */
    public function dispatchLoopShutdown() {

        if(Zend_Registry::isRegistered('session_app') && Zend_Registry::isRegistered('req_uri') && Zend_Registry::isRegistered('session')) {
            $session = Zend_Registry::get('session_app');
            $session->setlastRequest(Zend_Registry::get('req_uri'));
            $session->setlastRoute(Zend_Registry::get('reqContext'));
            // on verouille la session
            $this->sessionLock = Zend_Registry::get('session');
            $this->sessionLock->lock();
        }
    }

}