<?php
function INV_drop_table()
{
	global $wpdb;
	$wpdb->query( sprintf( "DROP TABLE IF EXISTS %s", $wpdb->order_price_table ) );
}

function INV_create_table()
{
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    global $wpdb;
    global $charset_collate;
    $wpdb->order_price_table = "{$wpdb->prefix}inv_order_price_form";
    $wpdb->order_price_setting_table = "{$wpdb->prefix}inv_order_price_form_setting";
    $sql_create_table = "CREATE TABLE {$wpdb->order_price_table} (
    id int NOT NULL AUTO_INCREMENT,
    email text NOT NULL,
    send_date datetime NOT NULL,
    PRIMARY KEY  (id)) $charset_collate; ";
    dbDelta( $sql_create_table );

    $sql_create_table = "CREATE TABLE {$wpdb->order_price_setting_table} (
    id int NOT NULL AUTO_INCREMENT,
    setting_name text NOT NULL,
    setting_value longtext NOT NULL,
    PRIMARY KEY  (id)) $charset_collate; ";
    dbDelta( $sql_create_table );
}

function INV_print_response()
{
    $resp = [];
    parse_str( $_POST['email'], $post_data );
    $user_email = $post_data['email'];

    if ( filter_var( $user_email, FILTER_VALIDATE_EMAIL ) ) {
        if ( !INV_exist_email_in_DB( $user_email ) ) {
            if ( INV_send_message( $user_email ) ) {
                logger( sprintf( "[%s] — Отправлено сообщение о заявке" . PHP_EOL, date( 'd.m.y H:i' ) ) );
                $resp = json_encode([
                    'status' => 'ok',
                    'msg'    => 'Благодарим за обращение, Ваша заявка находится в обработке, пожалуйста ожидайте.'
                ]);
                INV_add_email_to_DB( $user_email );
            } else {
                logger( sprintf( "[%s] — Не удалось отправить сообщение" . PHP_EOL, date( 'd.m.y H:i' ) ) );
                $resp = json_encode([
                    'status' => 'error',
                    'msg'    => 'На сервере что-то пошло не так, и заявка не была доставлена, попробуйте позже, либо обратитесь к администратору сайта.'
                ]);
            }
        } else {
            $resp = json_encode([
                'status' => 'error',
                'msg'    => 'Вы уже делали запрос, пожалуйста ожидайте, мы обязательно Вам ответим!'
            ]);
        }
    } else {
        $resp = json_encode([
            'status' => 'error',
            'msg'    => 'Вы ввели не корректный email, пожалуйста проверьте его.'
        ]);
    }

    die( $resp );
}

function INV_send_message( $email, $to = DEFAULT_EMAIL_TO_SEND )
{
    $msg_tpl = file_get_contents(INV_PLUGIN_PATH . '/res/message-template.html');
    $for_replace = [ '{{site-name}}', '{{email-adress}}', '{{date-and-time}}' ];
    $for_replace_value = [ $_SERVER['HTTP_HOST'], $email, date('d.m.Y H:i') ];
    $subject = 'Заявка на прайс';
    $message = str_replace($for_replace, $for_replace_value, $msg_tpl);
    $headers = ["From: БК-Маркет <noreplay@{$for_replace_value[0]}>", "content-type: text/html"];
    return wp_mail( $to, $subject, $message, $headers );
}

function INV_exist_email_in_DB( $email )
{
    global $wpdb;
    $wpdb->print_error();
    $current_date = date('Y.m.d');
    $check_exist_email_sql = "SELECT * FROM `{$wpdb->prefix}inv_order_price_form`
                              WHERE `email` = '$email' AND `send_date` >= '$current_date'";
    $email_exist = $wpdb->get_row($check_exist_email_sql);
    if ( $email_exist !== NULL) {
        return true;
    } else {
        return false;
    }
}

function INV_add_email_to_DB( $email )
{
    global $wpdb;
    $wpdb->print_error();
    $current_datetime = date('Y.m.d H:i');

    $res = $wpdb->insert(
        $wpdb->order_price_table,
        array( 'email' => $email, 'send_date' => $current_datetime ),
        array( '%s', '%s' )
    );

    if ( $res ) {
        logger( sprintf( "[%s] — В базу добавлен Email" . PHP_EOL, date( 'd.m.y H:i' ) ) );
    } else {
        logger( sprintf( "[%s] — Не удалось произвести вставку данных в базу" . PHP_EOL, date( 'd.m.y H:i' ) ) );
    }

    return $res;
}

function INV_set_options( $setting_name, $setting_val, $options = [] )
{
    global $wpdb;
    $wpdb->print_error();

    return $wpdb->insert(
        $wpdb->order_price_setting_table,
        array( 'setting_name' => $setting_name, 'setting_value' => $setting_value ),
        array( '%s', '%s' )
    );
}

function INV_get_DB_records( $limit = 20 )
{
    global $wpdb;
    $wpdb->print_error();
    return $wpdb->get_results( "SELECT * FROM `{$wpdb->prefix}inv_order_price_form` LIMIT $limit" );
}

function INV_format_to_SNG_date_view( $date_str )
{
    $date_arr = explode( ' ', $date_str );
    $date = explode( '-', $date_arr[0] );
    $date = array_reverse($date);
    $date = implode('.', $date);
    $time = explode( ':', $date_arr[1] );
    array_pop($time);
    $time = implode(':', $time);
    return $date . ' ' . $time;
}

function logger( $data, $log_path = INV_LOG_PATH )
{
    if ( !file_exists( $log_path ) ) {
        mkdir( $log_path );
    }
    file_put_contents( $log_path . '/log.txt', print_r( $data, true ), FILE_APPEND );
}

function js_console_log($data)
{
    if ( is_array($data) ) $data = json_encode($data);
    echo "<script>console.log($data)</script>";
}
