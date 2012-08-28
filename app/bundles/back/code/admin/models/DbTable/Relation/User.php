<?php

class Admin_Model_DbTable_Relation_User extends App_DbTable_Abstract {

    protected $_name = 'admin_relation_user_role';
    protected $_primary = array('user_role_id');
    protected $_referenceMap = array(
        'User' => array(
            'columns'       =>  array('user_id'),
            'refTableClass' =>  'Admin_Model_DbTable_User',
            'refColumns'    =>  array('user_id'),
            'onDelete'      =>  self::CASCADE,
            'onUpdate'      =>  self::RESTRICT
        ),
        
        'Role' => array(
            'columns'       =>  array('role_id'),
            'refTableClass' =>  'Admin_Model_DbTable_Role',
            'refColumns'    =>  array('role_id'),
            'onDelete'      =>  self::CASCADE,
            'onUpdate'      =>  self::RESTRICT
        ),
        
    );

}