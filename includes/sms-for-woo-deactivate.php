<?php
/**
 * * @package SmsForWoo
 */

class SmsForWooDeactivate {
    public static function deactivate(){
        flush_rewrite_rules();
    }
}