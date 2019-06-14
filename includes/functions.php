<?php

/*
* Удаление таблицы в базе
*/

function INV_drop_table() {
	global $wpdb;
	$wpdb->query( sprintf( "DROP TABLE IF EXISTS %s", $wpdb->order_price_table ) );
}

function INV_create_table() {
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    global $wpdb;
    global $charset_collate;
    $wpdb->order_price_table = "{$wpdb->prefix}inv_order_price_form";
    $sql_create_table = "CREATE TABLE {$wpdb->order_price_table} (
    id int NOT NULL AUTO_INCREMENT,
    message_text text NOT NULL,
    send_date date NOT NULL,
    PRIMARY KEY  (id)) $charset_collate; ";
    dbDelta( $sql_create_table );
}

function INV_validate_data() {
    parse_str($_POST['email'], $data);
    
    if ( filter_var($data['email'], FILTER_VALIDATE_EMAIL) ) {
        $resp = INV_send_message( $data['email'] );
    } else {
        $resp = json_encode([
            'status' => 'error',
            'msg'    => 'Вы ввели не корректный Email'
        ]);
    }
    
    die( $resp );
}

function INV_send_message( $email, $to = DEFAULT_EMAIL_TO_SEND ) {
    $msg_tpl = file_get_contents(INV_PLUGIN_PATH . '/res/message-template.html');
    $for_replace = [ '{{site-name}}', '{{email-adress}}', '{{date-and-time}}' ];
    $replaceble_values = [ $_SERVER['HTTP_HOST'], $email, date('d.m.Y H:i') ];
    $subject = 'Заявка на прайс';
    $message = str_replace($for_replace, $replaceble_values, $msg_tpl);
    $headers = array(
    "From: БК-Маркет <noreplay@{$replaceble_values[0]}>",
    "content-type: text/html"
    );
    if ( wp_mail( $to, $subject, $message, $headers) ) {
        // INV_add_email_to_DB( $message );
        return json_encode([
            'status' => 'ok',
            'msg'    => 'Ваш запрос находится в обработке, пожалуйста ожидайте.'
        ]);
    } else {
        return json_encode([
            'status' => 'error',
            'msg'    => 'К сожалению на сервере что-то пошло не так и запрос не был отправлен.'
        ]);
    }
}

function INV_get_prices_value($productID) {

    global $wpdb;

    $sql = "SELECT `purchase_price`,`selling_price`
            FROM `$wpdb->order_price_table`
            WHERE `product_id` = %d ORDER BY `update_date` DESC
            LIMIT 1";

    return $wpdb->get_row(
        $wpdb->prepare($sql, $productID), ARRAY_A
    );

}

function INV_set_product_price($data) {

    global $wpdb;
    $wpdb->print_error();

    return $wpdb->insert(
        $wpdb->order_price_table,
        $data
    );


}

function js_console_log($data) {
    if ( is_array($data) ) $data = json_encode($data);
    echo "<script>console.log($data)</script>";
}
