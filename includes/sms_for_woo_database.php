<?php

class SmsForWooDB
{
    public static function check_exists($phone)
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'smsforwoo';
        $count = $wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE phone = '$phone'");
        if ($count == 1) {
            return true;
        } else {
            return false;
        }
    }

    public static function count_entries()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'smsforwoo';
        $count = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");
        return $count;
    }

    public static function add_to_sms_db($name, $phone, $email)
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'smsforwoo';
        $wpdb->insert(
            $table_name,
            array(
                'time' => current_time('mysql'),
                'name' => $name,
                'phone' => $phone,
                'email' => $email,
            )
        );
    }

    public static function get_sms_db()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'smsforwoo';
        $result = $wpdb->get_results("SELECT * FROM $table_name");
        return $result;
    }
}
