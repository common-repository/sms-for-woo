<?php
/**
 * * @package SmsForWoo
 */
class ExportToCSV
{
    /**
    * Export function gets data from database into an array
    * Passes the array to array2csv fuction and gets the file from it
    * *
    */
    public static function export_csv()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'smsforwoo';
        $sql = "SELECT * FROM $table_name";
        $result = $wpdb->get_results($sql, 'ARRAY_A');
        //self::download_send_headers("sms_for_woo_export_" . date("Y-m-d") . ".csv");
        echo self::array2csv($result);
        die();
    }

    /**
    * Gets data as array and return is as csv
    * *
    */
    public static function array2csv($array)
    {
        if (count($array) == 0) {
            return null;
        }
        ob_start();
        $df = fopen("php://output", 'w');
        fputcsv($df, array_keys(reset($array)));
        foreach ($array as $row) {
            fputcsv($df, $row);
        }
        fclose($df);
        return ob_get_clean();
    }
}
