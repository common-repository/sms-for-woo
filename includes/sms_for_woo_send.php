<?php
function send_sms($message, $phone)
{
    $url = 'https://rest.global-voice.net/rest/send_sms';

    $api_token = get_option('sms_for_woo_apitoken');
    $connection_user = get_option('sms_for_woo_connection_user');
    $connection_password = get_option('sms_for_woo_connection_password');
    $site_name = get_option('sms_for_woo_from');
    $query_from = preg_replace('/\s+/', '', $site_name);
    $payload = json_encode(array("data" => "some data"));
    $query_message = urlencode($message);
    $final_url .= $url . "?from=$query_from&";
    $final_url .= "message=$query_message&";
    $final_url .= "to=$phone&";
    $final_url .= "username=$connection_user&";
    $final_url .= "password=$connection_password&";
    $endpoint = esc_url_raw($final_url);

    //use wordpress wp_remote_post();
    
    $options = [
        'method'      => 'POST',
        'body'        => $payload,
        'headers'     => [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'Authorization' => "Bearer $api_token",

        ],
        'timeout'     => 60,
        'redirection' => 5,
        'blocking'    => true,
        'httpversion' => '1.0',
        'sslverify'   => false,
        'data_format' => 'body',
    ];
     
    $result = wp_remote_post($endpoint, $options);
    
    if (is_wp_error($response)) {
        $error_message = $response->get_error_message();
        return "Something went wrong: $error_message";
    } else {
        return $result;
    }
}
