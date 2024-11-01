<?php
if (! current_user_can('manage_options')) {
    return;
}
require_once SMS_FWOO_DIR_PATH . 'includes/sms_for_woo_database.php';
$temp_nr_db = SmsForWooDB::get_sms_db();
$nr_array = array();
foreach ($temp_nr_db as $item) {
    //escaping phone number so i can be echoed as json
    array_push($nr_array, esc_js($item->phone));
}
?>

<div class="wrap">
    <h2 style="margin-bottom:50px">Send bulk sms</h2>
    <form action="" id="send_bulk_form">
        <table>
            <tbody>
                <tr>
                    <td style='width:120px;'><?php echo __('Available phone numbers', 'sms-for-woo') ?>
                    </td>
                    <td style='width:500px;padding-left:25px;'><?php echo SmsForWooDB::count_entries(); ?>
                    </td>
                </tr>
                <tr>
                    <td style='width:120px;'><?php echo __('Bulk Message', 'sms-for-woo') ?>
                    </td>
                    <td style='width:500px;height:150px;padding-left:25px;'><textarea name="sms_for_woo_bulk_message"
                            id="sms_for_woo_bulk_message" style='width:100%;resize:none' rows="4"></textarea>
                    </td>
                </tr>
            </tbody>
        </table>
        <div style="width:700px"><button type="submit" style="width:100px;height:35px;margin:0 auto;"><?php echo __('Send', 'sms-for-woo')?></button>
            <span id="sms_total_count" style="margin: 0 20px;margin-left:50px;"></span>
            <span id="sms_sent_count" style="margin: 0 20px;"></span>
            <span id="sms_rejected_count" style="margin: 0 20px;"></span>
        </div>
    </form>
    <h2 style="margin:50px 0 20px 0;">Customer Database</h2>
    <form method="post">
        <?php
            require_once SMS_FWOO_DIR_PATH . 'templates/smsforwoo_table.php';
            $obj = new SmsForWooTable;
            $obj->prepare_items();
            $obj->display();
        ?>
    </form>
</div>

<script>
    (function(jQuery) {
        jQuery.sanitize = function(input) {
            return input.replace(/<(|\/|[^>\/bi]|\/[^>bi]|[^\/>][^>]+|\/[^>][^>]+)>/g, '');
        };
    })(jQuery);
    jQuery('#send_bulk_form').submit(function(e) {
        e.preventDefault();
        var form_message = jQuery.sanitize(jQuery('#sms_for_woo_bulk_message').val());
        let
            phone_data = <?php echo wp_json_encode($nr_array); ?> ; //has already been escaped at $nr_array creation.
        let from_data =
            '<?php echo esc_js(get_option('sms_for_woo_from')); ?>';
        let api_token =
            '<?php echo esc_js(get_option('sms_for_woo_apitoken')); ?>';
        let connection_user =
            '<?php echo esc_js(get_option('sms_for_woo_connection_user')); ?>';
        let connection_password =
            '<?php echo esc_js(get_option('sms_for_woo_connection_password')); ?>';
        let gv_bulk_url = 'https://rest.global-voice.net/rest/bulk_send_sms';
        gv_bulk_url += "?username=" + connection_user;
        gv_bulk_url += "&password=" + connection_password;
        console.log(gv_bulk_url);
        let post_data = [];
        for (let i = 0; i < phone_data.length; i++) {
            let temp_item = {
                from: from_data,
                to: phone_data[i],
                message: form_message
            }
            post_data.push(temp_item);
        }

        let dataArray = phone_data;
        if (dataArray.length === 0) {
            jQuery('#sms_total_count').text('No users in the database.');
        } else {
            jQuery.ajax({
                url: gv_bulk_url,
                type: 'POST',
                data: JSON.stringify(post_data),
                contentType: 'application/json; charset=utf-8',
                dataType: 'json',
                headers: {
                    'Accept': 'application/json',
                    'Authorization': `Bearer ${api_token}`
                },
                success: function(data, status) {
                    //function on success
                    console.log(data);
                    if (status !== 'success') {
                        jQuery('#sms_total_count').text('Something went wrong!');
                    } else {
                        jQuery('#sms_total_count').text('Total: ' + data.totalCount);
                        jQuery('#sms_sent_count').text('Sent: ' + data.sentCount);
                        jQuery('#sms_rejected_count').text('Rejected: ' + data.rejectedCount);
                    }
                },
                error: function(jqXHR) {
                    // error handler
                    let local_error = JSON.parse(jqXHR.responseText);
                    jQuery('#sms_total_count').text('Error: ' + local_error.error_message);
                }
            });
        }
    });
</script>