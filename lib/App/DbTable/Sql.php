<?php

/*
 * This file is part of the Factory package.
 *
 * (c) Gerard Toko <gerardtoko@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class App_DbTable_Sql {

    /**
     *
     * @param array $params
     * @return type 
     */
    public static function install(array $params) {
        return self::abst($params, __METHOD__);
    }

    /**
     *
     * @param array $params
     * @return type 
     */
    public static function uninstall(array $params) {
        return self::abst($params, __METHOD__);
    }
    

    /**
     *
     * @param array $params
     * @param type $type
     * @throws Exception 
     */
    private static function abst(array $params, $type) {
        if (!empty($params['bundle']) && !empty($params['module'])) {

            $bundle = $params['bundle'];
            $module = $params['module'];

            $file = sprintf('%s/bundles/%s/code/%s/models/Shema/Sql.php', APPLICATION_PATH, $bundle, $module);

            if (file_exists($file)) {

                $class = App_Ressource::getShema(sprintf('%s/Sql', $module));

                $r = new Zend_Reflection_Class($class);
                $methods = $r->getMethods();

                foreach ($methods as $method) {
                    if ($method->name == $type) {
                        $class->$type();
                    }
                }
            } else {
                throw new Exception("$file no exist");
            }
        } else {
            throw new Exception("params error");
        }
    }

}