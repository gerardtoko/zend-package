<?php

/*
 * This file is part of the Factory package.
 *
 * (c) Gerard Toko <gerardtoko@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class App_TinyMce {

    public static function inc($width = "100%", $height = "600"){
        $path = sprintf("%s/web/etc/tinymce/tiny_mce.inc", DIR_PATH);
        include $path;
    }
}