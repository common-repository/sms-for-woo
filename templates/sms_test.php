<?php
if (! current_user_can('manage_options')) {
    return;
}



?>
<div class="wrap">
    <h1><?php echo esc_html(get_admin_page_title()); ?>
    </h1>
    <h3>Send test sms</h3>
    <form action="" id="send_test_message">
        <table>
            <tbody>
                <tr>
                    <td style='width:120px;'><?php echo __('Phone number', 'sms-for-woo')?>
                    </td>
                    <td style='width:500px;'><input type="tel" name="sms_for_woo_phone" id="sms_for_woo_phone"
                            style='width:100%'></input>
                    </td>
                </tr>
                <tr>
                    <td style='width:120px;'><?php echo __('Message', 'sms-for-woo')?>
                    </td>
                    <td style='width:500px;height:150px'><textarea name="sms_for_woo_message" id="sms_for_woo_message"
                            style='width:100%;height:100%'></textarea>
                    </td>
                </tr>
            </tbody>
        </table>
        <div style="width:700px"><button type="submit" style="width:100px;height:35px;margin:0 auto;"><?php echo __('Send', 'sms-for-woo')?></button>
        </div>
        <p style="display: none;height:30px;" id="sent_notification" value="">
        </p>
        <p style="display: none;width:" id="sent_notification2" value="">Request url:</p>
        <p style="" id="sent_request" value="">
        <p style="display: none;" id="sent_notification3" value="">Response:</p>
        <p style="" id="sent_response" value="">
        </p>
    </form></br></br>
    <h3>Get account data</h3>
    <form action="" id="get_account_data">
        <table>
            <tbody>
                <tr>
                    <td style='width:120px;'><?php echo __('User email', 'sms-for-woo')?>
                    </td>
                    <td style='width:250px'><input type="email" name="sms_for_woo_email" id="sms_for_woo_email"
                            style='width:100%'
                            value="<?php echo esc_attr(get_option('sms_for_woo_username')); ?>"></input>
                    </td>
                </tr>
                <tr>
                    <td style='width:120px;'><?php echo __('Password', 'sms-for-woo')?>
                    </td>
                    <td style='width:250px;'><input type="password" name="sms_for_woo_password"
                            id="sms_for_woo_password" style='width:100%;'
                            value="<?php echo esc_attr(get_option('sms_for_woo_password')); ?>"></input>
                    </td>
                </tr>
            </tbody>
        </table>
        <p><button type="submit" style="width:100px;height:35px"><?php echo __('Get data', 'sms-for-woo')?></button>
        </p>
        <p style="display: none;height:30px;" id="get_notification" value="">
        </p>
        <p style="display: none;height:30px;" id="get_error" value="">
        </p>
        <div style="display: none;text-align:right;" id="account_data">
            <h4 style="text-align:left;">Account Data</h4>
            <table style="border: 1px solid grey;border-radius:5px;padding:20px;">
                <tr>
                    <td>Account Id:</td>
                    <td id="account_id" style="padding: 0 0 0 20px;" value=""></td>
                </tr>
                <tr>
                    <td>Balance Updated:</td>
                    <td id="balance_updated" style="padding: 0 0 0 20px;" value=""></td>
                </tr>
                <tr>
                    <td>Balance:</td>
                    <td id="balance_value" style="padding: 0 0 0 20px;" value=""></td>
                </tr>
                <tr>
                    <td>Manager email:</td>
                    <td id="manager_email" style="padding: 0 0 0 20px;" value=""></td>
                </tr>
            </table>
        </div>

        </p>
    </form>
</div>

<script>
    (function(jQuery) {
        jQuery.sanitize = function(input) {
            /*
            var output = input.replace(/<script[^>]*?>.*?<\/script>/gi, '').
                        replace(/<[\/\!]*?[^<>]*?>/gi, '').
                        replace(/<style[^>]*?>.*?<\/style>/gi, '').
                        replace(/<![\s\S]*?--[ \t\n\r]*>/gi, '');
            return output;
            */
            return input.replace(/<(|\/|[^>\/bi]|\/[^>bi]|[^\/>][^>]+|\/[^>][^>]+)>/g, '');
        };
    })(jQuery);
    jQuery('#send_test_message').submit(function(e) {
        e.preventDefault();
        let sms_phone = jQuery.sanitize(jQuery('#sms_for_woo_phone').val());
        let sms_message_raw = jQuery.sanitize(jQuery('#sms_for_woo_message').val());
        let sms_message = sms_message_raw;
        let page_name =
            "<?php echo get_option('sms_for_woo_from'); ?>";
        page_name = encodeURIComponent(page_name);

        let gv_url = 'https://rest.global-voice.net/rest/send_sms';
        gv_url += "?from=" + page_name;
        gv_url += "&message=" + encodeURIComponent(sms_message);
        gv_url += "&to=" + sms_phone;
        gv_url +=
            "&username=<?php echo esc_js(get_option('sms_for_woo_connection_user')) ?>";
        gv_url +=
            "&password=<?php echo esc_js(get_option('sms_for_woo_connection_password')); ?>";
        let data_obj = {
            somedata: 'somedata'
        }
        let api_token =
            "<?php echo esc_js(get_option('sms_for_woo_apitoken')); ?>";
        let data = JSON.stringify(data_obj);
        jQuery.ajax({
            url: gv_url,
            type: 'POST',
            data: data,
            contentType: 'application/json; charset=utf-8',
            dataType: 'json',
            headers: {
                'Accept': 'application/json',
                'Authorization': `Bearer ${api_token}`
            },
            success: function(data, status) {
                //function on succes
                if (status !== 'success') {
                    jQuery('#sent_notification').text('Something went wrong!');
                } else {
                    jQuery('#sent_notification').text('Post made to api.');
                    jQuery('#sent_request').text(gv_url);
                    jQuery('#sent_response').text(JSON.stringify(data));
                }
                jQuery('#sent_notification').show();
                jQuery('#sent_notification2').show();
                jQuery('#sent_notification3').show();
            },
            error: function(jqXHR) {
                // error handler
                jQuery('#sent_notification').text('Post made to api.');
                jQuery('#sent_request').text(gv_url);
                jQuery('#sent_response').text(jqXHR.responseText);
                jQuery('#sent_notification').show();
                jQuery('#sent_notification2').show();
                jQuery('#sent_notification3').show();
            }
        });

    });
    jQuery('#get_account_data').submit(function(e) {
        e.preventDefault();
        let sms_username = jQuery.sanitize(jQuery('#sms_for_woo_email').val());
        let sms_password = jQuery.sanitize(jQuery('#sms_for_woo_password').val());
        let gv_account_url = 'https://rest.global-voice.net/rest/account';
        let user_data = sms_username + ":" + sms_password;
        let base64user = btoa(user_data);
        let data = JSON.stringify({
            placeholder: 'data'
        });

        jQuery.ajax({
            url: gv_account_url,
            type: 'GET',
            dataType: 'json',
            headers: {
                'Accept': 'application/json',
                'Authorization': `Basic ${base64user}`
            },
            success: function(data, status) {
                //function on success
                if (status !== 'success') {
                    jQuery('#get_notification').text('Something went wrong!');
                } else {
                    jQuery('#get_notification').text('Get req sent to api.');
                    jQuery('#account_id').text(data[0].id);
                    jQuery('#balance_updated').text(data[0].balance_updated);
                    jQuery('#balance_value').text(data[0].balance + " " + data[0].currency_code);
                    jQuery('#manager_email').text(data[0].manager_email);
                }
                jQuery('#get_error').hide();
                jQuery('#get_notification').show();
                jQuery('#account_data').show();
            },
            error: function(jqXHR) {
                // error handler
                let local_error = JSON.parse(jqXHR.responseText);
                jQuery('#get_notification').text('Get req sent to api.');
                jQuery('#get_error').text('Error: ' + local_error.error_message);
                jQuery('#get_error').show();
                jQuery('#get_notification').show();
                jQuery('#account_data').hide();
            }
        });

    });
</script>