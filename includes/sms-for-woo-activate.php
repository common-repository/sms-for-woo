<?php
/**
 * * @package SmsForWoo
 */

 class SmsForWooActivate
 {
     public static function activate()
     {
         flush_rewrite_rules();
         //create database
         if (! get_option('sfw_db_version')) {
             self::sfw_db_install();
         }
     }

     public static function sfw_db_install()
     {
         global $wpdb;
         global $sfw_db_version;
         $sfw_db_version = '1.0';

         $table_name = $wpdb->prefix . 'smsforwoo';
         $charset_collate = $wpdb->get_charset_collate();
        
         $sql = "CREATE TABLE $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
            name varchar(55) DEFAULT '' NOT NULL,
            phone varchar(12) DEFAULT '' NOT NULL,
            email varchar(55) DEFAULT '' NOT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";
    
         require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
         dbDelta($sql);
         add_option('sfw_db_version', $sfw_db_version);
     }
 }
