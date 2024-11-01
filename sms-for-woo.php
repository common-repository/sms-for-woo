<?php
/**
 * @package SmsForWoo
 * Plugin Name:       Sms for woo
 * Plugin URI:        #
 * Description:       A simple sms plugin for woocommerce
 * Version:           1.1.2
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Global Voice SRL
 * Author URI:        https://www.global-voice.net/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       sms-for-woo
 * Domain Path:       /languages
 */

//exit if directly accessed
if (!defined('ABSPATH')) {
    die;
}

//define variable for path to this plugin
define('SMS_FWOO_LOCATION', dirname(__FILE__));
define('SMS_FWOO_LOCATION_URL', plugins_url('', __FILE__));
define('SMS_FWOO_DIR_PATH', plugin_dir_path(__FILE__));

class SmsForWoo
{
    public $plugin_bname;

    public function __construct()
    {
        $this->plugin_bname = plugin_basename(__FILE__);
    }

    public function register()
    {
        //add scripts and styles
        add_action('admin_enqueue_scripts', array($this, 'enqueue'));
        //add admin menu
        add_action('admin_menu', array($this, 'add_admin_pages'));
        //add settings to plugin list page
        add_filter("plugin_action_links_$this->plugin_bname", array($this, 'settings_link'));
        //add fields to account page
        add_action('admin_init', array($this, 'fields_for_account'));
        //add fields to settings page
        add_action('admin_init', array($this, 'fields_for_settings'));
        //connect to woocommerce status change hook
        add_action('woocommerce_order_status_changed', array($this, 'status_changed'));
        //add optout to checkout
        add_action('woocommerce_after_order_notes', array($this, 'order_optout'));
        add_action('woocommerce_checkout_update_order_meta', array($this, 'update_order_meta'));
        //export table to csv
        add_action('wp_ajax_export_csv', array($this, 'export_csv'));
    }

    public function settings_link($links)
    {
        array_push($links, '<a href="admin.php?page=sms_for_woo">Settings</a>');
        return $links;
    }

    public function add_admin_pages()
    {
        add_menu_page('SMS For Woo Plugin', 'SMS For Woo', 'manage_options', 'sms_for_woo', array($this, 'admin_index'), 'dashicons-smartphone', 100);
        add_submenu_page('sms_for_woo', 'SMS For Woo Account', 'Account', 'manage_options', 'sms_for_woo_account', array($this, 'admin_sms_account'), 90);
        add_submenu_page('sms_for_woo', 'SMS For Woo Settings', 'Settings', 'manage_options', 'sms_for_woo_settings', array($this, 'admin_sms_settings'), 90);
        add_submenu_page('sms_for_woo', 'SMS For Woo Bulksms', 'Bulk SMS', 'manage_options', 'sms_for_woo_bulk', array($this, 'admin_bulk_sms'), 90);
        add_submenu_page('sms_for_woo', 'SMS Test', 'SMS Test', 'manage_options', 'sms_for_woo_test', array($this, 'admin_sms_test'), 100);
    }

    public function fields_for_account()
    {
        add_settings_section('sms_for_woo_connection_section', null, null, 'sms_for_woo_account');
        //user field
        add_settings_field('sms_for_woo_from', 'SMS From (max 11 char)', array($this, 'sms_from_html'), 'sms_for_woo_account', 'sms_for_woo_connection_section');
        register_setting('sms_for_woo_connection', 'sms_for_woo_from', array('sanitize_callback' => 'sanitize_text_field', 'default' => $this->populate_from_field()));
        //user field
        add_settings_field('sms_for_woo_username', 'Login Username', array($this, 'username_html'), 'sms_for_woo_account', 'sms_for_woo_connection_section');
        register_setting('sms_for_woo_connection', 'sms_for_woo_username', array('sanitize_callback' => 'sanitize_text_field', 'default' => ''));
        //password field
        add_settings_field('sms_for_woo_password', 'Login Password', array($this, 'password_html'), 'sms_for_woo_account', 'sms_for_woo_connection_section');
        register_setting('sms_for_woo_connection', 'sms_for_woo_password', array('sanitize_callback' => 'sanitize_text_field', 'default' => ''));
        //api field
        add_settings_field('sms_for_woo_apitoken', 'Api token (required)', array($this, 'api_token_html'), 'sms_for_woo_account', 'sms_for_woo_connection_section');
        register_setting('sms_for_woo_connection', 'sms_for_woo_apitoken', array('sanitize_callback' => 'sanitize_textarea_field', 'default' => ''));
        //Connection Username
        add_settings_field('sms_for_woo_connection_user', 'API Connection username (required)', array($this, 'connection_username_html'), 'sms_for_woo_account', 'sms_for_woo_connection_section');
        register_setting('sms_for_woo_connection', 'sms_for_woo_connection_user', array('sanitize_callback' => 'sanitize_text_field', 'default' => ''));
        //api field
        add_settings_field('sms_for_woo_connection_password', 'API Connection password (required)', array($this, 'connection_password_html'), 'sms_for_woo_account', 'sms_for_woo_connection_section');
        register_setting('sms_for_woo_connection', 'sms_for_woo_connection_password', array('sanitize_callback' => 'sanitize_text_field', 'default' => ''));
        add_option('sms_for_woo_temp', '');
        // echo "<div class='wrap'><div style='background-color:#fff;text-align:center;width:900px;margin:0 auto;margin-top:65px;white-space: pre-wrap;word-wrap: break-word;'>", get_option('sms_for_woo_temp'), "</div></div>" ;
    }

    public function fields_for_settings()
    {
        $order_statuses = wc_get_order_statuses();
        add_settings_section('sms_for_woo_settings_section', null, null, 'sms_for_woo_settings');
        add_settings_field('sms_for_woo_settings_note', '', array($this, 'settings_note_html'), 'sms_for_woo_settings', 'sms_for_woo_settings_section');
        add_settings_field('sms_for_woo_settings_all', 'Enable sending on order status change', array($this, 'settings_enableall_html'), 'sms_for_woo_settings', 'sms_for_woo_settings_section');
        register_setting('sms_for_woo_messages', 'sms_for_woo_settings_all', array('sanitize_callback' => 'sanitize_text_field', 'default' => '0'));
        foreach ($order_statuses as $key => $value) {
            $status_str = substr($key, 3);
            add_settings_field('sms_for_woo_staus_' . $status_str, $value, array($this, 'message_html'), 'sms_for_woo_settings', 'sms_for_woo_settings_section', array('statusName' => $status_str));
            register_setting('sms_for_woo_messages', 'sms_for_woo_status_' . $status_str, array('sanitize_callback' => 'sanitize_text_field', 'default' => '0'));
            register_setting('sms_for_woo_messages', 'sms_for_woo_' . $status_str . '_message', array('sanitize_callback' => 'sanitize_text_field', 'default' => ''));
        }
    }

    public function sms_from_html()
    {
        ?><input name='sms_for_woo_from' type="text" maxlength="11"
    value="<?php echo esc_attr(get_option('sms_for_woo_from')); ?>"><?php
    }

    public function username_html()
    {
        ?><input name='sms_for_woo_username' type="text"
    value="<?php echo esc_attr(get_option('sms_for_woo_username')); ?>"><?php
    }

    public function password_html()
    {
        ?><input name='sms_for_woo_password' type="password"
    value="<?php echo esc_attr(get_option('sms_for_woo_password')); ?>"><?php
    }

    public function api_token_html()
    {
        ?><textarea name="sms_for_woo_apitoken" style='width:600px; resize:none;' rows="3">
                <?php echo esc_html(get_option('sms_for_woo_apitoken')); ?></textarea><?php
    }

    public function connection_username_html()
    {
        ?><input name='sms_for_woo_connection_user' type="text"
    value="<?php echo esc_attr(get_option('sms_for_woo_connection_user')); ?>"><?php
    }

    public function connection_password_html()
    {
        ?><input name='sms_for_woo_connection_password' type="text"
    value="<?php echo esc_attr(get_option('sms_for_woo_connection_password')); ?>"><?php
    }

    public function settings_note_html()
    {
        ?>
<div
    style="margin-bottom:20px;padding:25px;background-color:white;border-radius:5px;box-shadow: 0px 0px 10px 1px rgba(0,0,0,0.2);">
    <p>Note: In order for [ORDER_CURIER] and [CURIER_CODE] to work custom fields need to be set-up for the order with
        the name: Curier and Curier_code</p>
</div>
<?php
    }

    public function settings_enableall_html()
    {
        ?>
<div
    style="display:flex;flex-wrap: wrap;justify-content:left;align-items:center;border-left: 1px solid grey;padding-left: 25px;height:50px;">
    <input style="margin-right:35px;" name="sms_for_woo_settings_all" type="checkbox" value="1" <?php checked(get_option('sms_for_woo_settings_all'), "1")?>
    >
</div>
<?php
    }

    public function message_html($args)
    {
        $statusName = $args['statusName']; ?>
<div style="display:flex;flex-wrap: wrap;justify-content:space-evenly;align-items:center;border-left: 1px solid grey;">
    <input style="margin-right:35px;"
        name="sms_for_woo_status_<?php echo $statusName; ?>"
        type="checkbox" value="1" <?php checked(get_option('sms_for_woo_status_' . $statusName), "1")?>
    >
    <div>
        <textarea class="sms_textarea"
            id="sms_for_woo_<?php echo $statusName; ?>_message"
            name="sms_for_woo_<?php echo $statusName; ?>_message"
            style='width:600px; resize:none;'
            rows="3"><?php echo esc_html(get_option('sms_for_woo_' . $statusName . '_message')); ?></textarea>
        <p>Characters: <?php echo strlen(get_option('sms_for_woo_' . $statusName . '_message')) ?>/160
        </p>

    </div>
    <div style="min-width:400px">
        <h4 style="text-align:center;">Tags:</h4>
        <div style="display:flex;flex-wrap: wrap;justify-content:center;width:90%;margin:0 auto;">
            <a href="#"
                onclick='insertAtCaret("sms_for_woo_<?php echo $statusName; ?>_message", this.firstElementChild.innerText);return false;'>
                <p style="margin: 0 7px;">[SELLER_PAGE]</p>
            </a>
            <a href="#"
                onclick='insertAtCaret("sms_for_woo_<?php echo $statusName; ?>_message", this.firstElementChild.innerText);return false;'>
                <p style="margin: 0 7px;">[BILLING_FIRST]</p>
            </a>
            <a href="#"
                onclick='insertAtCaret("sms_for_woo_<?php echo $statusName; ?>_message", this.firstElementChild.innerText);return false;'>
                <p style="margin: 0 7px;">[BILLING_LAST]</p>
            </a>
            <a href="#"
                onclick='insertAtCaret("sms_for_woo_<?php echo $statusName; ?>_message", this.firstElementChild.innerText);return false;'>
                <p style="margin: 0 7px;">[SHIPPING_FIRST]</p>
            </a>
            <a href="#"
                onclick='insertAtCaret("sms_for_woo_<?php echo $statusName; ?>_message", this.firstElementChild.innerText);return false;'>
                <p style="margin: 0 7px;">[SHIPPING_LAST]</p>
            </a>
            <a href="#"
                onclick='insertAtCaret("sms_for_woo_<?php echo $statusName; ?>_message", this.firstElementChild.innerText);return false;'>
                <p style="margin: 0 7px;">[ORDER_NUMBER]</p>
            </a>
            <a href="#"
                onclick='insertAtCaret("sms_for_woo_<?php echo $statusName; ?>_message", this.firstElementChild.innerText);return false;'>
                <p style="margin: 0 7px;">[ORDER_DATE]</p>
            </a>
            <a href="#"
                onclick='insertAtCaret("sms_for_woo_<?php echo $statusName; ?>_message", this.firstElementChild.innerText);return false;'>
                <p style="margin: 0 7px;">[ORDER_TOTAL]</p>
            </a>
            <a href="#"
                onclick='insertAtCaret("sms_for_woo_<?php echo $statusName; ?>_message", this.firstElementChild.innerText);return false;'>
                <p style="margin: 0 7px;">[ORDER_CURIER]</p>
            </a>
            <a href="#"
                onclick='insertAtCaret("sms_for_woo_<?php echo $statusName; ?>_message", this.firstElementChild.innerText);return false;'>
                <p style="margin: 0 7px;">[CURIER_CODE]</p>
            </a>
        </div>
    </div>
</div>
<?php
    }

    public function populate_from_field()
    {
        $site_name = get_option('blogname');
        $from_field = '';
        if (strlen($site_name) > 11) {
            $from_field = substr($site_name, 0, 11);
        } else {
            $from_field = $site_name;
        }
        return $from_field;
    }

    public function admin_index()
    {
        //html for admin index page
        require_once plugin_dir_path(__FILE__) . 'templates/admin_index.php';
    }

    public function admin_sms_account()
    {
        //html for sms settings
        require_once plugin_dir_path(__FILE__) . 'templates/sms_account.php';
    }

    public function admin_sms_settings()
    {
        //html for sms settings
        require_once plugin_dir_path(__FILE__) . 'templates/sms_settings.php';
    }

    public function admin_bulk_sms()
    {
        require_once plugin_dir_path(__FILE__) . 'templates/sms_bulk.php';
    }

    public function admin_sms_test()
    {
        //html for test subpage
        require_once plugin_dir_path(__FILE__) . 'templates/sms_test.php';
    }

    public function enqueue()
    {
        //add style file
    }

    public function export_csv()
    {
        require_once plugin_dir_path(__FILE__) . 'templates/export_csv.php';
        ExportToCSV::export_csv();
        wp_die();
    }

    public function sanitize(string $data): string
    {
        return strip_tags(
            stripslashes(
                sanitize_text_field(
                    filter_input(INPUT_POST, $data)
                )
            )
        );
    }

    public function order_optout($checkout)
    {
        echo '<div class="optout_checkbox_container">';
        woocommerce_form_field(
            'sms_for_woo_optout',
            array(
                'type' => 'checkbox',
                'class' => array('input-checkbox'),
                'label' => __('I wish not to receive promotional messages.', 'sms_for_woo'),
            ),
            $checkout->get_value('sms_for_woo_optout')
        );
        echo '</div>';
    }

    public function update_order_meta($order_id)
    {
        if (isset($_POST['sms_for_woo_optout'])) {
            update_post_meta($order_id, 'sms_for_woo_optout', $this->sanitize($_POST['sms_for_woo_optout']));
        }
    }

    public function status_changed($order_id, $checkout = null)
    {
        if (get_option('sms_for_woo_settings_all') != '1') {
            return;
        }
        require_once plugin_dir_path(__FILE__) . 'includes/sms_for_woo_send.php';
        require_once plugin_dir_path(__FILE__) . 'includes/sms_for_woo_clean.php';
        require_once plugin_dir_path(__FILE__) . 'includes/sms_for_woo_database.php';
        if (!class_exists('WooCommerce')) {
            die;
        }
        global $woocommerce;
        $order = new WC_Order($order_id);
        $data = $order->get_data();
        $status = $data['status'];
        $is_sms_active = get_option("sms_for_woo_status_$status");
        $template_message = get_option("sms_for_woo_$status" . "_message");
        $order_name = SmsForWooClean::replace_nonascii($data['billing']['first_name'] . " " . $data['billing']['last_name']);
        $order_phone = SmsForWooClean::clean_phone_number($data['billing']['phone'], $data['shipping']['country']);
        $sms_message = SmsForWooClean::clean_message($template_message, $data);
        if ($is_sms_active == '0' || empty($is_sms_active)) {
            //update_option('sms_for_woo_temp', 'Sms is disabled for status:' . $status);
        } else {
            if ($order_phone != '' && $sms_message != '') {
                $result = send_sms($sms_message, $order_phone);
                //update_option('sms_for_woo_temp', $result);
                $phone_exists = SmsForWooDB::check_exists($order_phone);
                $order_meta = get_post_meta($order_id);
                // update_option('sms_for_woo_temp', 'Checkbox meta: ' . print_r($order_meta['sms_for_woo_optout']));
                if (!$phone_exists && !isset($order_meta['sms_for_woo_optout'])) {
                    SmsForWooDB::add_to_sms_db($order_name, $order_phone, $data['billing']['email']);
                }
                $entries_nr = SmsForWooDB::count_entries();
            // update_option('sms_for_woo_temp', 'Phone numbers: ' . $entries_nr);
            } else {
                // update_option('sms_for_woo_temp', 'Phone or Message are empty.');
            }
        }
    }
}

if (class_exists('SmsForWoo')) {
    $smsForWoo = new SmsForWoo();
    $smsForWoo->register();
}

//activation
require_once plugin_dir_path(__FILE__) . 'includes/sms-for-woo-activate.php';
register_activation_hook(__FILE__, array('SmsForWooActivate', 'activate'));

//deactivation
require_once plugin_dir_path(__FILE__) . 'includes/sms-for-woo-deactivate.php';
register_deactivation_hook(__FILE__, array('SmsForWooDeactivate', 'deactivate'));
