<?php

class Admin_Model_DbTable_User extends App_DbTable_Abstract {
    protected $_name = 'admin_user';
    protected $_primary = array('user_id');
    protected $_dependentTables = array('Admin_Model_DbTable_Relation_User');
}