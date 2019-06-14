<?php 

define(WP_AJAX_ACTION_NAME, 'inv_order_price');
define(DEFAULT_EMAIL_TO_SEND, 'sp.inversion@yandex.kz');


add_action( 'wp_enqueue_scripts', 'add__order_price__style_and_scripts' );
function add__order_price__style_and_scripts() {
    wp_enqueue_style( 'inv_order_price', INV_PLUGIN_PATH . '/res/style.css' );
    wp_enqueue_script( 'inv_order_price', INV_PLUGIN_PATH . '/res/action.js', array('jquery'), '1.0.0', true );
}

add_action( 'wp_ajax_' . WP_AJAX_ACTION_NAME, 'INV_validate_data' ); 
add_action( 'wp_ajax_nopriv_' . WP_AJAX_ACTION_NAME, 'INV_validate_data' );

add_action( 'wp_footer', function() { ?>

<script>const ajax_url = '<?php echo admin_url("admin-ajax.php");?>'</script>
<div id="order_price" style="display: none;">
    <button class="action_btn"><?php echo file_get_contents(INV_PLUGIN_PATH . '/res/dollar-symbol.svg'); ?></button>
    <div class="form">
        <form action="<?= WP_AJAX_ACTION_NAME ?>">
            <input type="email" name="email" required="" placeholder="Ваш email" class="form_email_field">
            <button class="send_form_btn">Запросить прайс</button>
        </form>
    <div class="message"></div>
    </div>
</div>

<?php }, 99 ); ?>
