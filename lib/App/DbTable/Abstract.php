<?php

/*
 * This file is part of the Factory package.
 *
 * (c) Gerard Toko <gerardtoko@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class App_DbTable_Abstract extends Zend_Db_Table_Abstract {

    protected $_conditions = array();

    /**
     *
     * @param type $key
     * @return type 
     */
    public function getNameCol($key) {
        $key = (int) $key;
        $info = $this->getInfo();
        if (isset($info[$key])) {
            return $info[$key];
        }
    }

    /**
     *
     * @return type 
     */
    public function count() {
        $select = $this->select();
        $select->from($this, array('count(*) as c'));
        $rows = $this->fetchRow($select)->toArray();
        return (int) $rows['c'];
    }

    /**
     *
     * @param type $name
     * @return type 
     */
    public function getInfo($name = "cols") {
        return $this->info($name);
    }

    /**
     *
     * @param type $value
     * @param type $col
     * @param array $field
     * @return type 
     */
    public function findOne($value, $col = 0, array $field = array()) {
        $req = $this->select();
        $req = $this->setFilter($req, $field);

        if (is_int($col)) {
            $req->where(sprintf("%s = '%s'", $this->getNameCol($col), $value));
        } else {
            $req->where(sprintf("%s = '%s'", $col, $value));
        }

        $row = $this->fetchRow($req);
        $row = is_null($row) ? array() : $row->toArray();
        return $row;
    }

    /**
     *
     * @param array $conditions
     * @param array $field
     * @return type 
     */
    public function findAll(array $conditions = array(), array $field = array()) {
        $req = $this->select();
        $req = $this->setFilter($req, $field);
        $req = $this->setCondition($req, $conditions);

        $rows = $this->fetchAll($req);
        $rows = is_null($rows) ? array() : $rows->toArray();
        return $rows;
    }

    /**
     *
     * @param array $data
     * @return type 
     */
    public function add(array $data) {

        $col = $this->info("cols");
        if (in_array('created', $col)) {
            if (empty($data['created'])) {
                $data['created'] = time();
            }
        }

        return parent::insert($data);
    }

    /**
     *
     * @param array $data
     * @param array $conditions
     * @return type 
     */
    public function save(array $data, array $conditions = array()) {

        $col = $this->info("cols");
        if (in_array('updated', $col)) {
            if (empty($data['updated'])) {
                $data['updated'] = time();
            }
        }

        return parent::update($data, $conditions);
    }

    /**
     *
     * @param type $id
     * @param type $col
     * @return type 
     */
    public function deleteOne($id, $col = 0) {

        if (is_int($col)) {
            $req = $this->getAdapter()
                    ->quoteInto(sprintf('%s = ?', $this->getNameCol($col)), $id);
        } else {
            $req = $this->getAdapter()
                    ->quoteInto(sprintf('%s = ?', $col), $id);
        }

        return $this->delete($req);
    }

    /**
     *
     * @param type $condition
     * @return type 
     */
    public function deleteAll($condition = array()) {
        return $this->delete($condition);
    }

    /**
     *
     * @param type $id
     * @return type 
     */
    public function deleteWithDependency($id) {
        $row = $this->find($id)->current();
        return $row->delete();
    }

    /**
     *
     * @param array $array
     * @return boolean 
     */
    public function deleteAllWithDependency(array $array) {
        foreach ($array as $id) {
            $this->deleteWithDependency($id);
        }
        return true;
    }

    /**
     *
     * @param type $req
     * @param array $conditions
     * @return type 
     */
    protected function setCondition($req, array $conditions) {

        if (!empty($conditions)) {
            foreach ($conditions as $key_condition => $value_condition) {

                if (is_array($value_condition)) {
                    foreach ($value_condition as $value) {
                        $req->$key_condition($value);
                    }
                } else {
                    $req->$key_condition($value_condition);
                }
            }
        }

        return $req;
    }

    /**
     *
     * @param type $req
     * @param array $field
     * @return type 
     */
    protected function setFilter($req, array $field) {
        $req = empty($field) ? $req->from($this) : $req->from($this, $field);
        return $req;
    }

}