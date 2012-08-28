<?php

class Admin_Model_Shema_Sql {

    public function install() {

        $db = Zend_Registry::get('db');

        $eventInstance = App_Event::getInstance();
        $message = true;
        $eventInstance->dispatch(strtolower(sprintf("%s_%s", __CLASS__, __METHOD__)), $message);


        //Create Tables
        $db->query("DROP TABLE IF EXISTS `admin_permission`");
        $db->query("CREATE TABLE `admin_permission` (
  `permission_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `bundle` tinytext,
  `module` tinytext,
  `ressource` tinytext,
  `action` tinytext,
  `created` int(11) DEFAULT NULL,
  `updated` int(11) DEFAULT NULL,
  PRIMARY KEY (`permission_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;");


        $db->query("DROP TABLE IF EXISTS `admin_relation_permission_role`");
        $db->query("CREATE TABLE `admin_relation_permission_role` (
  `permission_role_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `role_id` int(11) DEFAULT NULL,
  `permission_id` int(11) DEFAULT NULL,
  `created` int(11) DEFAULT NULL,
  `updated` int(11) DEFAULT NULL,
  PRIMARY KEY (`permission_role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;");



        $db->query("DROP TABLE IF EXISTS `admin_user`");

        $db->query("CREATE TABLE `admin_user` (
  `user_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'User ID',
  `firstname` varchar(32) DEFAULT NULL COMMENT 'User First Name',
  `lastname` varchar(32) DEFAULT NULL COMMENT 'User Last Name',
  `username` varchar(40) DEFAULT NULL COMMENT 'User Login',
  `email` varchar(128) DEFAULT NULL COMMENT 'User Email',
  `pass` varchar(40) DEFAULT NULL COMMENT 'User Password',
  `status` int(10) NOT NULL DEFAULT '1' COMMENT 'User Is Active',
  `logdate` int(11) DEFAULT NULL COMMENT 'User Last Login Time',
  `lognum` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'User Login Number',
  `created` int(11) NOT NULL COMMENT 'User Created Time',
  `updated` int(11) DEFAULT NULL COMMENT 'User Modified Time',
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `UNIQUE` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;");



        $db->query("DROP TABLE IF EXISTS `admin_role`");
        $db->query("CREATE TABLE `admin_role` (
  `role_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` text,
  `created` int(11) DEFAULT NULL,
  `updated` int(11) DEFAULT NULL,
  PRIMARY KEY (`role_id`)
)ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;");


        $db->query("DROP TABLE IF EXISTS `admin_relation_user_role`");
        $db->query("CREATE TABLE `admin_relation_user_role` (
  `user_role_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `role_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `created` int(11) DEFAULT NULL,
  `updated` int(11) DEFAULT NULL,
  PRIMARY KEY (`user_role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;");



        //Add
        $db->query("INSERT INTO `admin_user` (`user_id`, `firstname`, `lastname`, `username`, `email`, `pass`, `status`, `logdate`, `lognum`, `created`, `updated`)
VALUES (1,'','','admin','admin@example.com','d033e22ae348aeb5660fc2140aec35850c4da997',1,NULL,0,1340707573,1340708007);");

        $db->query("INSERT INTO `admin_role` (`role_id`, `name`, `created`, `updated`)
VALUES (1,'admin',1340707444,NULL);");
        $db->query("INSERT INTO `admin_relation_user_role` (`user_role_id`, `role_id`, `user_id`, `created`, `updated`)
VALUES (1,1,1,1340708007,NULL);");
    }

    public function uninstall() {
        $db = Zend_Registry::get('db');

        $eventInstance = App_Event::getInstance();
        $message = true;
        $eventInstance->dispatch(strtolower(sprintf("%s_%s", __CLASS__, __METHOD__)), $message);

        $db->query(" DROP TABLE `admin_relation_permission_role`");
        $db->query(" DROP TABLE `admin_permission`");
        $db->query(" DROP TABLE `admin_user`");
        $db->query(" DROP TABLE `admin_role`");
        $db->query(" DROP TABLE `admin_relation_user_role`");
    }

}