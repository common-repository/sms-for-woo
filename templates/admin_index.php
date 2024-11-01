<?php
if (! current_user_can('manage_options')) {
    return;
}

?>
<style>
    .mainbody_container {
        display: flex;
    }

    .connection__guide {
        width: 50%;
    }

    .logo__container {
        width: 50%;
        display: flex;
        flex-direction: column;
    }

    .logo__container img {
        margin-bottom: 25px;
    }

    .logo__container a,
    .logo__container a:hover,
    .logo__container a:active,
    .logo__container a:visited,
    .logo__container a:focus {
        outline: 0;
        box-shadow: none !important;
        border: none;
        color: transparent;
    }

    @media screen and (max-width: 1024px) {
        .mainbody_container {
            display: flex;
            flex-direction: column-reverse;
        }

        .connection__guide {
            width: 100%;
        }

        .logo__container {
            width: 100%;
            margin: 25px 0;
            justify-content: center;
            align-items: center;
        }
    }
</style>
<div class="wrap">
    <h1>SMS For Woo</h1>
    <div class="mainbody_container">
        <div class="connection__guide">
            <h2>Connection guide:</h2>
            <div class="ordered_list_container">
                <ol>
                    <li>Go to <a href="https://www.global-voice.net/">https://www.global-voice.net/</a>
                    </li>
                    <li>Register an account</li>
                    <li>Then go to <a
                            href="https://retail.global-voice.net/sign-in/">https://retail.global-voice.net/sign-in/</a>
                        and
                        sign-in.</li>
                    <li>Navigate to "API connections" .</li>
                    <li>Click "Add connection".</br><img
                            src="<?php echo SMS_FWOO_LOCATION_URL . '/assets/images/create_connection.jpg'; ?>"
                            alt="sms add connection" style="width:300px;heigth:174px;border: 2px solid #006aFF;">
                        </br>
                        <span> -Note: for dynamic ip you can use wildcard for ip (Ex. Ip Address: *.*.*.* )</span>
                    </li>
                    <li>Copy user name and password.</br><img
                            src="<?php echo SMS_FWOO_LOCATION_URL . '/assets/images/user_and_password.jpg'; ?>"
                            alt="sms add connection" style="width:333px;heigth:93px;border: 2px solid #006aFF;"></li>
                    <li>Paste them into SMS for Woo "Account" section.</br><img
                            src="<?php echo SMS_FWOO_LOCATION_URL . '/assets/images/account_settings.jpg'; ?>"
                            alt="sms add connection" style="width:300px;heigth:174px;border: 2px solid #006aFF;">
                    </li>
                    <li>Navigate to "Tokens" on the global-voice.net "API connetions" area.</li>
                    <li>Click "Get token".</br><img
                            src="<?php echo SMS_FWOO_LOCATION_URL . '/assets/images/get_token.jpg'; ?>"
                            alt="get api token" style="width:600px;heigth:93px;border: 2px solid #006aFF;"></li>
                    <li>Copy the token.</br><img
                            src="<?php echo SMS_FWOO_LOCATION_URL . '/assets/images/copy_token.jpg'; ?>"
                            alt="copy api token" style="width:653px;heigth:93px;border: 2px solid #006aFF;"></li>
                    <li>Paste it into SMS for Woo "Account" section.</br><img
                            src="<?php echo SMS_FWOO_LOCATION_URL . '/assets/images/token_setting.jpg'; ?>"
                            alt="copy api token" style="width:400px;heigth:190px;border: 2px solid #006aFF;">
                    </li>
                    <li>Click "Save changes" and you are done.</li>
                </ol>
            </div>
        </div>
        <div class="logo__container">
            <a href="https://www.global-voice.net/" target="_blank" class="logo-link"><img
                    src="<?php echo SMS_FWOO_LOCATION_URL . '/assets/images/logo-global-voice-transparent.png'; ?>"
                    alt="global voice logo" style="width:300px;"></a>
            <iframe width="560" height="315" src="https://www.youtube.com/embed/F_gM2KUxRlI"
                title="YouTube video player" frameborder="0"
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                allowfullscreen></iframe>
        </div>
    </div>
</div>
<?php