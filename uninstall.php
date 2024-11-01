<?php

/**
 * @package SmsForWoo
 *
 */

 if (! defined('WP_UNINSTALL_PLUGIN')) {
     die;
 }
 // Delete all the plugin settings
 delete_option('sms_for_woo_temp');
 delete_option('sms_for_woo_from');
 delete_option('sms_for_woo_username');
 delete_option('sms_for_woo_password');
 delete_option('sms_for_woo_apitoken');
 delete_option('sms_for_woo_connection_user');
 delete_option('sms_for_woo_connection_password');
 delete_option('sms_for_woo_settings_all');
 $order_statuses = wc_get_order_statuses();
 foreach ($order_statuses as $key => $value) {
     $status_str = substr($key, 3);
     delete_option('sms_for_woo_status_' . $status_str);
     delete_option('sms_for_woo_' . $status_str . '_message');
 }
 // Clear Database stored data
 global $wpdb;
 $table_name = $wpdb->prefix . 'smsforwoo';
 $result = $wpdb->query("DROP TABLE IF EXISTS $table_name");
 delete_option('sfw_db_version');
