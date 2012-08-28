<?php


class App_Parser_Yaml_Dumper {

    const ASSOC = 'ASSOC';
    const NUM = 'NUM';

    public static function encode($array, $inline = 0) {

        foreach ($array as $key => $value) {
            if (is_array($value)) {
                switch (self::getType($array)) {
                    case self::ASSOC:
                        echo str_repeat("  ", $inline) . $key . ': ' . "\n";
                        break;
                    case self::NUM:
                        echo str_repeat("  ", $inline) . '- ' . "\n";
                        break;
                }
                self::encode($value, $inline + 2);
            } else {
                self::write(array('key' => $key, 'value' => $value, 'parent' => $array), $inline);
            }
        }
    }

    public static function write(array $array, $inline) {

        switch (self::getType($array['parent'])) {
            case self::ASSOC:
                echo str_repeat("  ", $inline) . $array['key']. ': ' . $array['value'] . "\n";
                break;
            case self::NUM:
                echo str_repeat("  ", $inline) . '- ' . $array['value'] . "\n";
                break;
        }
    }

    public static function getType($value) {
        return (is_array($value) && array_diff_key($value, array_keys(array_keys($value)))) ? self::ASSOC : self::NUM;
    }

}