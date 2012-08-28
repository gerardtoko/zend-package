<?php

class Admin_Model_DbTable_Role extends App_DbTable_Abstract {
    protected $_name = 'admin_role';
    protected $_primary = array('role_id');
    protected $_dependentTables = array('Admin_Model_DbTable_Relation_User', 'Admin_Model_DbTable_Relation_Permission');
}