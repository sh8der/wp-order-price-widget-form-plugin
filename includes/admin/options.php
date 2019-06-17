<?php 

define(WP_AJAX_ADMIN_SAVE_SETTING_ACTION_NAME, 'inv_order_price__save_setting');

add_action( 'admin_enqueue_scripts', function(){
    wp_enqueue_style( 'order-price_admin-style', INV_PLUGIN_PATH .'/res/admin.css' );
    wp_enqueue_script( 'inv_order_price__admin', INV_PLUGIN_PATH . '/res/admin-actions.js', array('jquery'), '1.0.0', true );

}, 99 );

add_action('admin_menu', function(){
    add_menu_page(
        'Настройка виджета заказа прайса',
        'Запрос прайса',
        'edit_others_posts',
        'order-price-setting',
        'render__order_price_setting'    
    );
});

function render__order_price_setting(){?>
    <div class="wrap">
        <h2><?php echo get_admin_page_title() ?></h2>

        <table class="wp-list-table widefat fixed striped posts">
            <thead>
                <tr>
                    <th>ID записи</th>
                    <th>Email</th>
                    <th>Дата отправки запроса</th>
                </tr>
            </thead>
            <?php foreach ( INV_get_DB_records() as $record ):?>
                <tr>
                    <td><?= $record->id ?></td>
                    <td><?= $record->email ?></td>
                    <td><?= INV_format_to_SNG_date_view($record->send_date) ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    
        <div class="order-price-tabs">
            <ul class="tabl-list">
                <li data-target="setting" class="active">Настройки</li>
                <li data-target="strings" >Строки</li>
            </ul>
            <div id="setting" class="tab open">
                <form action="<?=WP_AJAX_ADMIN_SAVE_SETTING_ACTION_NAME?>">
                    <label for="to_email">Почта на которую приходят заявки</label>
                    <input type="email" name="to_email" id="to_email" placeholder="E-mail">
                    <button class="send_btn" disabled>Сохранить</button>
                </form>
            </div>
            <div id="strings" class="tab">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Neque animi deleniti error repellendus, iure totam! Numquam atque commodi excepturi laborum explicabo, rerum dolorum veritatis odio, velit consectetur, magnam autem necessitatibus.</div>
        </div>

    </div>
    <?php
}

?>
