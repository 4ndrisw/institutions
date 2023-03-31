<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_231 extends App_module_migration {
    public function up() {
        // Perform database upgrade here

            //$CI = &get_instance();
            //$CI->db->query("ALTER TABLE `" . $contacts . "` ADD `institution_emails` TINYINT(1) DEFAULT '0'  AFTER `ticket_emails`;");

            //$CI->db->query('ALTER TABLE `' . db_prefix() . 'clients` ADD `is_institution` TINYINT(1) DEFAULT NULL `;");

        /*
        ALTER TABLE `tblclients` ADD `is_institution` TINYINT(1) NULL DEFAULT NULL,  ADD   INDEX  `is_institution` (`is_institution`);
        ALTER TABLE `tblclients` ADD `number` VARCHAR(20) NULL DEFAULT NULL, ADD INDEX `number` (`number`);
        ALTER TABLE `tblclients` ADD `prefix` VARCHAR(10) NULL DEFAULT NULL;
        ALTER TABLE `tblclients` ADD `number_format` VARCHAR(30) NULL DEFAULT NULL;
        ALTER TABLE `tblclients` ADD `hash` VARCHAR(32) NOT NULL;

        ALTER TABLE `tblclients` ADD `head_id` INT(11) NULL DEFAULT NULL;
        ALTER TABLE `tblclients` ADD `phone` INT(11) NULL DEFAULT NULL;
        */
    }
}