<?php

class Admin_Model_DbTable_Permission extends App_DbTable_Abstract {
    protected $_name = 'admin_permission';
    protected $_primary = array('permission_id');
    protected $_dependentTables = array('Admin_Model_DbTable_Relation_Permission');
}