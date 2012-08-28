<?php

class Admin_Model_DbTable_Relation_Permission extends App_DbTable_Abstract {

    protected $_name = 'admin_relation_permission_role';
    protected $_primary = array('permission_role_id');
    protected $_referenceMap = array(
        'Role' => array(
            'columns' => array('role_id'),
            'refTableClass' => 'Admin_Model_DbTable_Role',
            'refColumns' => array('role_id'),
            'onDelete' => self::CASCADE,
            'onUpdate' => self::RESTRICT
        ),
        'Permission' => array(
            'columns' => array('permission_id'),
            'refTableClass' => 'Admin_Model_DbTable_Permission',
            'refColumns' => array('permission_id'),
            'onDelete' => self::CASCADE,
            'onUpdate' => self::RESTRICT
        )
    );

}