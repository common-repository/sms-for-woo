<?php

if (!class_exists('WP_List_Table')) {
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}

class SmsForWooTable extends WP_List_Table
{
    public function __construct()
    {
        parent::__construct(array(
            'singular' => __('Customer', 'sms-for-woo'),
            'plural' => __('Customers', 'sms-for-woo'),
            'ajax' => true
        ));
    }

    /** * Prepare the items for the table to process
    * * @return Void
    */
    public function prepare_items()
    {
        // $this->_column_headers = $this->get_column_info();
        $columns = $this->get_columns();
        $hidden = $this->get_hidden_columns();
        $sortable = $this->get_sortable_columns();
        $this->_column_headers = array(
            $columns,
            $hidden,
            $sortable
        );
        /** Process bulk action */
        $this->process_bulk_action();
        $per_page = $this->get_items_per_page('records_per_page', 15);
        $current_page = $this->get_pagenum();
        $total_items = self::record_count();
        $data = self::get_records($per_page, $current_page);
        $this->set_pagination_args(
            ['total_items' => $total_items, //WE have to calculate the total number of items
                    'per_page' => $per_page // WE have to determine how many items to show on a page
                    ]
        );
        $this->items = $data;
    }

    /**
    *Retrieve records data from the database
    * * @param int $per_page
    * @param int $page_number
    * * @return mixed
    */
    public static function get_records($per_page = 15, $page_number = 1)
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'smsforwoo';
        $sql = "SELECT * FROM $table_name";
        // if (isset($_REQUEST['s'])) {
        //     $sql.= ' WHERE name LIKE "%' . $_REQUEST['s'] . '%" OR phone LIKE "%' . $_REQUEST['s'] . '%"';
        // }
        if (!empty($_REQUEST['orderby'])) {
            $sql.= ' ORDER BY ' . esc_sql($_REQUEST['orderby']);
            $sql.= !empty($_REQUEST['order']) ? ' ' . esc_sql($_REQUEST['order']) : ' ASC';
        }
        $sql.= " LIMIT $per_page";
        $sql.= ' OFFSET ' . ($page_number - 1) * $per_page;
        $result = $wpdb->get_results($sql, 'ARRAY_A');
        return $result;
    }

    /**
    * Override the parent columns method. Defines the columns to use in your listing table
    * * @return Array
    */
    public function get_columns()
    {
        $columns = [
            'cb' => '<input type="checkbox" />',
            'id' => __('Id', 'sms-for-woo') ,
            'name' => __('Name', 'sms-for-woo') ,
            'phone' => __('Phone', 'sms-for-woo') ,
            'email' => __('Email', 'sms-for-woo') ,
            'time' => __('Added date', 'sms-for-woo')
        ];
        return $columns;
    }

    public function get_hidden_columns()
    {
        // Setup Hidden columns and return them
        return array();
    }

    /**
    * Columns to make sortable.
    * * @return array
    */
    public function get_sortable_columns()
    {
        $sortable_columns = array(
            'id' => array('id',false) ,
            'name' => array('name',false) ,
            'email' => array('email',false) ,
            'time' => array('time',true)
        );
        return $sortable_columns;
    }

    /**
    * Render the bulk edit checkbox
    * * @param array $item
    * * @return string
    */
    public function column_cb($item)
    {
        return sprintf('<input type="checkbox" name="bulk-delete[]" value="%s" />', $item['id']);
    }

    /**
    * Render table data
    * * @param array $item
    * * @return string
    *----------------------------------------------
    */
    public function column_id($item)
    {
        return sprintf('<p>%s</p>', $item['id']);
    }
    public function column_name($item)
    {
        return sprintf('<p>%s</p>', $item['name']);
    }
    public function column_phone($item)
    {
        return sprintf('<p>%s</p>', $item['phone']);
    }
    public function column_email($item)
    {
        return sprintf('<p>%s</p>', $item['email']);
    }
    public function column_time($item)
    {
        return sprintf('<p>%s</p>', $item['time']);
    }
    /**
     * End of table data
    *----------------------------------------------
    */
     


    /**
    * Returns an associative array containing the bulk action
    * * @return array */
    public function get_bulk_actions()
    {
        $actions = ['bulk-delete' => 'Delete'];
        return $actions;
    }

    public function process_bulk_action()
    {
        // Detect when a bulk action is being triggered...
        if ('delete' === $this->current_action()) {
            // In our file that handles the request, verify the nonce.
            $nonce = esc_attr($_REQUEST['_wpnonce']);
            if (!wp_verify_nonce($nonce, 'sfw_delete_records')) {
                die();
            } else {
                self::delete_records(absint($_GET['record']));
                $redirect = admin_url('admin.php?page=sms_for_woo_bulk');
                wp_redirect(esc_url(add_query_arg()));
                exit;
            }
        }
    
        // If the delete bulk action is triggered
        if ((isset($_POST['action']) && $_POST['action'] == 'bulk-delete')
        || (isset($_POST['action2']) && $_POST['action2'] == 'bulk-delete')) {
            $delete_ids = esc_sql($_POST['bulk-delete']);
            // loop over the array of record IDs and delete them
            foreach ($delete_ids as $id) {
                self::delete_records($id);
            }
    
            $redirect = admin_url('admin.php?page=sms_for_woo_bulk');
            wp_redirect(esc_url(add_query_arg()));
            exit;
        }
    }

    /**
    * Delete a record record.
    * * @param int $id customer ID
    */
    public static function delete_records($id)
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'smsforwoo';
        $wpdb->delete($table_name, ['id' => $id], ['%d']);
    }

    /**
    *Text displayed when no record data is available
    */
    public function no_items()
    {
        _e('No record found in the database.', 'sms-for-woo');
    }

    /**
    * Returns the count of records in the database.
    * * @return null|string
    */
    public static function record_count()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'smsforwoo';
        $sql = "SELECT COUNT(*) FROM $table_name";
        return $wpdb->get_var($sql);
    }

    /**
    * Overrides the table nav to add custom button
    * *
    */
    protected function display_tablenav($which)
    {
        ?>
<div class="tablenav <?php echo esc_attr($which); ?>">
    <?php if ($this->has_items()) : ?>
    <div class="alignleft actions bulkactions">
        <?php $this->bulk_actions($which); ?>
    </div>
    <?php endif;
        if ('top' === $which) :
    ?>
    <input type="button" id="exportcsv_top" class="button action" value="Export CSV">
    <?php
        endif;
        $this->pagination($which); ?>

    <br class="clear" />
</div>
<script type="text/javascript">
    jQuery('#exportcsv_top').click(function(e) {
        e.preventDefault();
        var data = {
            'action': 'export_csv'
        };
        count = 0;
        // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
        jQuery.post(ajaxurl, data, function(response) {
            count++;
            if (count < 2) {
                console.log(response);
                var today = new Date();
                var dd = String(today.getDate()).padStart(2, '0');
                var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
                var yyyy = today.getFullYear();
                today = dd + '/' + mm + '/' + yyyy;
                var filename = 'sms_for_woo_export_' + today + '.csv';
                var blob = new Blob([response], {
                    type: 'text/csv;charset=utf-8;'
                });
                if (navigator.msSaveBlob) { // IE 10+
                    navigator.msSaveBlob(blob, filename);
                } else {
                    var link = document.createElement("a");
                    if (link.download !== undefined) { // feature detection
                        // Browsers that support HTML5 download attribute
                        var url = URL.createObjectURL(blob);
                        link.setAttribute("href", url);
                        link.setAttribute("download", filename);
                        link.style.visibility = 'hidden';
                        document.body.appendChild(link);
                        link.click();
                        document.body.removeChild(link);
                    }
                }
            }

        });
    });
</script>
<?php
    }

    public function download_send_headers($filename)
    {
        // disable caching
        $now = gmdate("D, d M Y H:i:s");
        header("Expires: Tue, 03 Jul 2122 06:00:00 GMT");
        header("Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate");
        header("Last-Modified: {$now} GMT");
    
        // force download
        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");
    
        // disposition / encoding on response body
        header("Content-Disposition: attachment;filename={$filename}");
        header("Content-Transfer-Encoding: binary");
    }
}
