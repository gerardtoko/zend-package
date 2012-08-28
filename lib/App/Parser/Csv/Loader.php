<?php

/*
 * This file is part of the Factory package.
 *
 * (c) Gerard Toko <gerardtoko@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class Csv_Loader {

    protected $file = null;
    protected $line = array();
    protected $col = array();

    public function __construct($file) {
        $this->file = $file;
    }

    public function decode() {
        $lines = file($this->file);
        $row = 0;
        foreach ($lines as $line) {
            if ($row == 0) {
                $data = explode(';', trim((string) $line, ';'));

                foreach ($data as $key => $value) {
                    $data[trim($key)] = trim((string) $value);
                }
                $this->col = $data;
            } else {
                $data = explode(';', trim($line, ';'));
                foreach ($data as $key => $value) {
                    $data[trim($key)] = trim((string) $value);
                }
                $this->line[] = array_combine($this->col, $data);
            }
            $row++;
        }
        return $this->line;
    }

}