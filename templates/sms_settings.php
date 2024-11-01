<?php
if (! current_user_can('manage_options')) {
    return;
}

?>
<div class="wrap">
    <h1>Settings Page</h1>
    <form action="options.php" method="POST">
        <?php
            settings_fields('sms_for_woo_messages');
            do_settings_sections('sms_for_woo_settings');
            submit_button();
        ?>
    </form>
</div>
<script type="text/javascript">
    let sms_settings_textareas = document.querySelectorAll(".sms_textarea");
    for (let i = 0; i < sms_settings_textareas.length; i++) {
        let sms_element = sms_settings_textareas[i];
        sms_element.onkeyup = function() {
            let sms_length = this.value.length;
            let sms_remaining = 160 - sms_length;
            if (sms_remaining >= 0) {
                this.nextElementSibling.innerText = 'Characters: ' + sms_length + '/160';
            } else {
                this.nextElementSibling.innerText = 'Too many characters!';
            }

        }
    }
</script>
<script>
    function insertAtCaret(areaId, text) {
        var txtarea = document.getElementById(areaId);
        if (!txtarea) {
            return;
        }

        var scrollPos = txtarea.scrollTop;
        var strPos = 0;
        var br = ((txtarea.selectionStart || txtarea.selectionStart == '0') ?
            "ff" : (document.selection ? "ie" : false));
        if (br == "ie") {
            txtarea.focus();
            var range = document.selection.createRange();
            range.moveStart('character', -txtarea.value.length);
            strPos = range.text.length;
        } else if (br == "ff") {
            strPos = txtarea.selectionStart;
        }

        var front = (txtarea.value).substring(0, strPos);
        var back = (txtarea.value).substring(strPos, txtarea.value.length);
        txtarea.value = front + text + back;
        strPos = strPos + text.length;
        if (br == "ie") {
            txtarea.focus();
            var ieRange = document.selection.createRange();
            ieRange.moveStart('character', -txtarea.value.length);
            ieRange.moveStart('character', strPos);
            ieRange.moveEnd('character', 0);
            ieRange.select();
        } else if (br == "ff") {
            txtarea.selectionStart = strPos;
            txtarea.selectionEnd = strPos;
            txtarea.focus();
        }

        txtarea.scrollTop = scrollPos;
    }
</script>

<?php