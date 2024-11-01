<?php
/**
 * * @package SmsForWoo
 */

 class SmsForWooClean
 {
     public static function clean_phone_number($number, $country_code)
     {
         //phone codes
         require_once SMS_FWOO_DIR_PATH . 'includes/country-phone-codes.php';
         $prefix = $sms_for_woo_country_code[$country_code]['dial_code'];
         $phone_nr ='';
         if ($number == '' || is_null($number)) {
             return $phone_nr;
         }
         //process phone number to match api requirements
         if (substr($number, 0, strlen($prefix)) == $prefix) {
             $phone_nr = $number;
             return $phone_nr;
         }
         if (substr($number, 0, 1) == '+') {
             $phone_nr = substr($number, 1);
             return $phone_nr;
         }
         if (substr($number, 0, 2) == '00') {
             $phone_nr = substr($number, 2);
             return $phone_nr;
         }
         if ($number[0] == '0' && $number[1] != '0') {
             $phone_nr = $prefix . substr($number, 1);
             return $phone_nr;
         }
         return $phone_nr;
     }

     public static function clean_message($message, $data)
     {
         $cleaned_message = '';
         if ($message == '' || is_null($message)) {
             return $cleaned_message;
         }
         $cleaned_message = $message;
         $order_date = $data['date_created'];
         $order_metadata = $data['meta_data'];
         $order_curier = '';
         $curier_code = '';
         foreach ($order_metadata as $item) {
             if ($item->key == 'Curier') {
                 $order_curier = $item->value;
             }
             if ($item->key == 'Curier_code') {
                 $curier_code = $item->value;
             }
         }
         unset($item);
         $tags_data = array(
            '[SELLER_PAGE]' => get_option('blogname'),
            '[BILLING_FIRST]' => self::replace_nonascii($data['billing']['first_name']),
            '[BILLING_LAST]' => self::replace_nonascii($data['billing']['last_name']),
            '[SHIPPING_FIRST]' => self::replace_nonascii($data['shipping']['first_name']),
            '[SHIPPING_LAST]' => self::replace_nonascii($data['shipping']['last_name']),
            '[ORDER_NUMBER]' => $data['id'],
            '[ORDER_DATE]' => $order_date->date('d-m-Y'),
            '[ORDER_TOTAL]' => $data['total'] . ' ' . $data['currency'],
            '[ORDER_CURIER]' => $order_curier,
            '[CURIER_CODE]' => $curier_code
        );
         foreach ($tags_data as $key => $value) {
             $cleaned_message = str_replace($key, $value, $cleaned_message);
         }
         return $cleaned_message;
     }

     public static function replace_nonascii($string)
     {
         $text = $string;

         // Single letters
         $text = preg_replace("/[∂άαáàâãªäă]/u", "a", $text);
         $text = preg_replace("/[∆лДΛдАÁÀÂÃÄĂ]/u", "A", $text);
         $text = preg_replace("/[ЂЪЬБъь]/u", "b", $text);
         $text = preg_replace("/[βвВ]/u", "B", $text);
         $text = preg_replace("/[çς©с]/u", "c", $text);
         $text = preg_replace("/[ÇС]/u", "C", $text);
         $text = preg_replace("/[δ]/u", "d", $text);
         $text = preg_replace("/[éèêëέëèεе℮ёєэЭ]/u", "e", $text);
         $text = preg_replace("/[ÉÈÊË€ξЄ€Е∑]/u", "E", $text);
         $text = preg_replace("/[₣]/u", "F", $text);
         $text = preg_replace("/[НнЊњ]/u", "H", $text);
         $text = preg_replace("/[ђћЋ]/u", "h", $text);
         $text = preg_replace("/[ÍÌÎÏ]/u", "I", $text);
         $text = preg_replace("/[íìîïιίϊі]/u", "i", $text);
         $text = preg_replace("/[Јј]/u", "j", $text);
         $text = preg_replace("/[ΚЌК]/u", 'K', $text);
         $text = preg_replace("/[ќк]/u", 'k', $text);
         $text = preg_replace("/[ℓ∟]/u", 'l', $text);
         $text = preg_replace("/[Мм]/u", "M", $text);
         $text = preg_replace("/[ñηήηπⁿ]/u", "n", $text);
         $text = preg_replace("/[Ñ∏пПИЙийΝЛ]/u", "N", $text);
         $text = preg_replace("/[óòôõºöοФσόо]/u", "o", $text);
         $text = preg_replace("/[ÓÒÔÕÖθΩθОΩ]/u", "O", $text);
         $text = preg_replace("/[ρφрРф]/u", "p", $text);
         $text = preg_replace("/[®яЯ]/u", "R", $text);
         $text = preg_replace("/[ГЃгѓ]/u", "r", $text);
         $text = preg_replace("/[ЅȘŞ]/u", "S", $text);
         $text = preg_replace("/[ѕșş]/u", "s", $text);
         $text = preg_replace("/[ТтȚ]/u", "T", $text);
         $text = preg_replace("/[τ†‡ț]/u", "t", $text);
         $text = preg_replace("/[úùûüџμΰµυϋύ]/u", "u", $text);
         $text = preg_replace("/[√]/u", "v", $text);
         $text = preg_replace("/[ÚÙÛÜЏЦц]/u", "U", $text);
         $text = preg_replace("/[Ψψωώẅẃẁщш]/u", "w", $text);
         $text = preg_replace("/[ẀẄẂШЩ]/u", "W", $text);
         $text = preg_replace("/[ΧχЖХж]/u", "x", $text);
         $text = preg_replace("/[ỲΫ¥]/u", "Y", $text);
         $text = preg_replace("/[ỳγўЎУуч]/u", "y", $text);
         $text = preg_replace("/[ζ]/u", "Z", $text);
         return $text;
     }
 }
