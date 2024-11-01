<?php
if (! current_user_can('manage_options')) {
    return;
}

?>
<div class="wrap">
    <h1>Account Page</h1>
    <form action="options.php" method="POST">
        <?php
            settings_fields('sms_for_woo_connection');
            do_settings_sections('sms_for_woo_account');
            submit_button();
        ?>
    </form>
</div>

<?php