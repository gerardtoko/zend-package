<?php

/*
 * This file is part of the Factory package.
 *
 * (c) Gerard Toko <gerardtoko@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

Class App_Cache {

    private static $_cacheManagerInstance = null;
    protected static $_defaultFrontendOptions = array(
        'name' => 'Core',
        'options' => array(
            'caching' => true,
            'cache_id_prefix' => 'factory_',
            'lifetime' => 14400,
            'loggin' => true,
            'write_control' => true,
            'automatic_serialization' => true,
            'automatic_cleaning_factor' => true,
            'ignore_user_abort' => false
        )
    );
    protected static $_write = true;
    protected static $_read = true;

    /**
     *
     * @return boolean 
     */
    public static function start() {

        $cache_dir = DIR_PATH . '/var/cache/default';
        if (!is_dir($cache_dir)) {
            mkdir($cache_dir, 0775, true);
            chmod($cache_dir, 0775);
        }

        $cache_default = array(
            'frontend' => self::$_defaultFrontendOptions,
            'backend' => array(
                'name' => 'File',
                'options' => array(
                    'cache_dir' => DIR_PATH . '/var/cache/default',
                    'read_control' => true,
                    'read_control_type' => 'crc32',
                    'hashed_directory_level' => 0,
                    'file_name_prefix' => 'factory',
                    'metatadatas_array_max_size' => 100
                )
            )
        );

        self::$_cacheManagerInstance = new Zend_Cache_Manager();
        self::$_cacheManagerInstance->setCacheTemplate('default', $cache_default);

        return true;
    }

    /**
     *
     * @param type $data
     * @param type $key
     * @param type $space
     * @return type 
     */
    public static function set($data, $key, $space = 'default') {
        if (self::getWriteMode()) {
            $cache_namespace = self::$_cacheManagerInstance->getCache($space);
            return $cache_namespace->save($data, $key);
        }
    }

    /**
     *
     * @param type $key
     * @param type $space
     * @return type 
     */
    public static function get($key, $space = 'default') {
        if (self::getReadMode()) {
            $cache_namespace = self::$_cacheManagerInstance->getCache($space);
            return $cache_namespace->load($key);
        }
    }

    /**
     *
     * @param type $space
     * @param type $key
     * @return type 
     */
    public static function clean($space = 'default', $key = null) {

        if (self::getWriteMode()) {
            $cache_namespace = self::$_cacheManagerInstance->getCache($space);
            if (is_null($key)) {
                return $cache_namespace->clean();
            }
            return $cache_namespace->remove($key);
        }
    }

    /**
     *
     * @return boolean 
     */
    public static function cleanAll() {
        if (self::getWriteMode()) {
            $caches = self::$_cacheManagerInstance->getCaches();
            foreach ($caches as $key_cache => $value_cache) {
                $cache_namespace = self::$_cacheManagerInstance->getCache($key_cache);
                $cache_namespace->clean();
            }
            return true;
        }
    }

    /**
     *
     * @param array $nameSpace
     * @param type $newFrontEnd
     * @throws Exception 
     */
    public static function setCacheNameSpace(array $nameSpace, $newFrontEnd = null) {

        $options = array();
        if (is_null($newFrontEnd)) {
            $file = APPLICATION_PATH . '/configs/cache.yml';
            if (file_exists($file)) {
                $config = App_Config::load($file);
                $options['frontend'] = !empty($config['cache']['frontend']) ?
                        $config['cache']['frontend'] : self::$_defaultFrontendOptions;
            } else {
                $options['frontend'] = self::$_defaultFrontendOptions;
            }
        }

        if (!empty($nameSpace)) {
            $name = $nameSpace['namespace'];
            $options['backend'] = $nameSpace['options'];
        } else {
            throw new Exception("no found space");
        }

        self::$_cacheManagerInstance->setCacheTemplate($name, $options);
    }

    /**
     *
     * @param type $value 
     */
    public static function setReadMode($value = true) {
        self::$_read = $value;
    }

    /**
     *
     * @return type 
     */
    public static function getReadMode() {
        return self::$_read;
    }

    /**
     *
     * @param type $value 
     */
    public static function setWriteMode($value = true) {
        self::$_write = $value;
    }

    /**
     *
     * @return type 
     */
    public static function getWriteMode() {
        return self::$_write;
    }

}