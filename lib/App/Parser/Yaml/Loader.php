<?php

class App_Parser_Yaml_Loader {

    const COMMENT_PATTERN = '#^\##';
    const ARRAY_NUM_PATTERN = '#^\-#';
    const ARRAY_ASSOC_PATTERN = '#[a-zA-Z0-9_.*]#';
    const ARRAY_VALUE = '__ARRAY__';
    const STRING_CURRENT = 'STRING_CURRENT';
    const INTEGER = '#^([-+]?)(\d+)$#';
    const FLOAT = '#[-+]?([0-9]*\.[0-9]{0,}+)#';
    const INDENT = 4;

    protected static $_BOOL = array('true', 'TRUE', 'false', 'FALSE');
    protected static $_NUL = array('null', 'NULL');
    protected static $_STRING = array('start' => '#^(\'|\")#', 'end' => '#(\'|\")$#'); //String
    protected static $_ARRAY_SEQEUNCE = array('start' => '#^(\[)#', 'end' => '#(\])$#');
    protected static $_ARRAY_MAPPING = array('start' => '#^(\{)#', 'end' => '#(\})$#');

    /**
     *
     * @param type $lines
     * @param type $indent
     * @return array
     */
    public static function decode(&$lines, $indent = 0) {

        $parse = array();

        foreach ($lines as $key => $line) {

            //Rewind lines
            if (self::parseSpace($line) < $indent) {
                reset($lines);
                break;
            }

            //Get Format
            $format = self::parseFormat($line);

            //Traitment of the Line
            $t = self::parseLine($format, $line, $lines);

            //Delete line
            unset($lines[$key]);

            //writer
            self::writer($lines, $parse, $indent, $t);
        }

        return $parse;
    }

    /**
     *
     * @param type $lines
     * @param type $parse
     * @param type $indent
     * @param type $t
     */
    public static function writer(&$lines, &$parse, &$indent, &$t) {

        //Filter Comments
        if (!empty($t)) {

            //Detection assoc/num
            if (!empty($t['key'])) {

                // Detection Array
                if ($t['value'] !== self::ARRAY_VALUE) {
                    $parse[$t['key']] = $t['value'];
                } else {
                    $parse[$t['key']] = App_Parser_Yaml_Loader::decode($lines, ($indent + self::INDENT));
                }
            } else {
                // Detection Array
                if ($t['value'] !== self::ARRAY_VALUE) {
                    $parse[] = $t['value'];
                } else {
                    $parse[] = Loader::decode($lines, ($indent + self::INDENT));
                }
            }
        }
    }

    /**
     *
     * @param type $line
     * @return type
     */
    public static function parseFormat($line) {
        $line = trim($line);
        if (!empty($line)) {
            $array = array(self::COMMENT_PATTERN, self::ARRAY_NUM_PATTERN, self::ARRAY_ASSOC_PATTERN);
            foreach ($array as $type) {
                if (preg_match($type, $line)) {
                    return $type;
                }
            }
        }else{
           return self::COMMENT_PATTERN;  
        }
    }

    /**
     *
     * @param type $format
     * @param type $line
     * @param type $lines
     * @return null
     */
    public static function parseLine($format, $line, &$lines) {

        switch ($format) {
            case self::ARRAY_NUM_PATTERN:
                $value = trim(substr(strstr($line, '-'), 1));
                if (!empty($value) || $value === '0') {
                    $value = self::parseValue($value, $lines);
                    return array('value' => $value);
                } else {
                    return array('value' => self::ARRAY_VALUE);
                }
                break;

            case self::ARRAY_ASSOC_PATTERN:
                $key = trim(strstr($line, ':', TRUE));
                $value = trim(substr(strstr($line, ':'), 1));
                if (!empty($value) || $value === '0') {
                    $value = self::parseValue($value, $lines);
                    return array('value' => $value, 'key' => $key);
                } else {
                    return array('value' => self::ARRAY_VALUE, 'key' => $key);
                }
                break;

            case self::COMMENT_PATTERN:
                return null;
                break;
        }
    }

    /**
     *
     * @param type $value
     * @param type $lines
     * @return null
     */
    public static function parseValue($value, &$lines) {

        // Parsing NUMERIC
        if (is_numeric($value)) {
            return self::parseValueNumeric($value);
        }

        // Parsing BOOL
        foreach (self::$_BOOL as $bool) {
            if ($bool == $value) {
                return self::parseValueBool($value);
            }
        }

        //Parsing NULL
        foreach (self::$_NUL as $null) {
            if ($null == $value) {
                return NULL;
            }
        }

        // Parsing ARRAY SEQUENCE
        if (preg_match(self::$_ARRAY_SEQEUNCE['start'], $value)) {
            return self::parseValueArraySequence($value, $lines);
        }

        // Parsing ARRAY MAPPING
        if (preg_match(self::$_ARRAY_MAPPING['start'], $value)) {
            return self::parseValueArrayMapping($value, $lines);
        }

        //ELSEIF Parsing STRING
        return self::parseValueString($value, $lines);
    }

    /**
     *
     * @param type $value
     * @return type
     */
    public static function parseValueBool($value) {
        $bool = (preg_match('#^f#i', $value)) ? FALSE : TRUE;
        return $bool;
    }

    /**
     *
     * @param type $value
     * @return type
     */
    public static function parseValueNumeric($value) {

        //Parsing INTEGER
        if (preg_match(self::INTEGER, $value)) {
            return (int) $value;
        }

        //Parsing FLOAT
        if (preg_match(self::FLOAT, $value)) {
            return (float) $value;
        }
    }

    /**
     *
     * @param type $value
     * @param type $lines
     * @return type
     */
    public static function parseValueString($value, &$lines) {

        //On test l'ouverture
        if (preg_match(self::$_STRING['start'], $value)) {

            $value = substr($value, 1);

            //On test s'il exite de fermeture
            if (preg_match(self::$_STRING['end'], $value)) {
                return (string) substr($value, 0, -1);
            } else {

                //first lines
                $value = $value . "\n";
                array_shift($lines);

                //On cherche la fermeture
                foreach ($lines as $key => $line) {
                    if (preg_match(self::$_STRING['end'], $line)) {
                        return $value .= substr(rtrim($line), 0, -1);
                    } else {
                        $value .= $line;
                        unset($lines[$key]); //On supprime les lines
                    }
                }
            }
        }
        return $value;
    }

    /**
     *
     * @param type $value
     * @param type $lines
     * @return type
     */
    public static function parseValueArraySequence($value, &$lines) {

        $array = array();

        //On test s'il exite de fermeture
        if (preg_match(self::$_ARRAY_SEQEUNCE['end'], $value)) {
            $explode = explode(',', substr($value, 1, -1));
            self::sequenceElement($explode, $array, $lines);
        } else {
            $explode = explode(',', substr(rtrim($value), 1));
            self::sequenceElement($explode, $array, $lines);
            array_shift($lines);

            //On cherche la fermeture
            foreach ($lines as $key => $line) {
                if (self::parseFormat($line) != self::COMMENT_PATTERN) {
                    if (preg_match(self::$_ARRAY_SEQEUNCE['end'], $line)) {
                        $explode = explode(',', substr(rtrim($line), 0, -1));
                        self::sequenceElement($explode, $array, $lines);
                        unset($lines[$key]);
                        break;
                    } else {
                        $explode = explode(',', trim($line));
                        self::sequenceElement($explode, $array, $lines);
                        unset($lines[$key]); //On supprime les lines
                    }
                }
            }
        }
        return (array) $array;
    }

    /**
     *
     * @param type $value
     * @param type $lines
     * @return type
     */
    public static function parseValueArrayMapping($value, &$lines) {

        $array = array();

        //On test s'il exite de fermeture
        if (preg_match(self::$_ARRAY_MAPPING['end'], $value)) {
            $explode = explode(',', substr($value, 1, -1));
            self::mappingElement($explode, $array, $lines);
        } else {

            $explode = explode(',', substr(rtrim($value), 1));
            self::mappingElement($explode, $array, $lines);
            array_shift($lines);

            //On cherche la fermeture
            foreach ($lines as $key => $line) {
                if (self::parseFormat($line) != self::COMMENT_PATTERN) {
                    if (preg_match(self::$_ARRAY_MAPPING['end'], $line)) {
                        $explode = explode(',', substr(rtrim($line), 0, -1));
                        self::mappingElement($explode, $array, $lines);
                        unset($lines[$key]);
                        break;
                    } else {
                        $explode = explode(',', trim($line));
                        self::mappingElement($explode, $array, $lines);
                        unset($lines[$key]); //On supprime les lines
                    }
                }
            }
        }
        return (array) $array;
    }

    /**
     *
     * @param type $explode
     * @param type $array
     */
    public static function mappingElement(&$explode, &$array, &$lines) {

        //Verification
        foreach ($explode as $key => $value) {
            if (!strstr($value, ':')) {
                $explode[$rewind_key] = sprintf('%s,%s', $explode[$rewind_key], $value);
                unset($explode[$key]);
            } else {
                $rewind_key = $key;
            }
        }

        foreach ($explode as $key => $elt) {
            if (!empty($elt)) {
                $elt = trim($elt);
                $key = trim(strstr($elt, ':', TRUE));
                $value = trim(substr(strstr($elt, ':'), 1));

                if (preg_match('#^\".+\"$#', $value)) {
                    $value = substr($value, 1, -1);
                }
                $array[$key] = self::parseValue($value, $lines);
            }
        }
    }

    public static function sequenceElement(&$explode, &$array, &$lines) {

        foreach ($explode as $elt) {
            if (!empty($elt)) {
                $array[] = self::parseValue(trim($elt), $lines);
            }
        }
    }

    /**
     *
     * @param type $line
     * @return type
     */
    public static function parseSpace($line) {
        $format = self::parseFormat($line);
        if (self::COMMENT_PATTERN == $format) {
            return true;
        } else {
            return substr_count($line, " ") - substr_count(ltrim($line), " ");
        }
    }

}