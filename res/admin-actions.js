jQuery(document).ready(function() {
    let $tabList          = jQuery('.order-price-tabs .tabl-list li');
    let $sendEmailBtn     = jQuery('.order-price-tabs form button');
    let $sendEmailInput   = jQuery('.order-price-tabs form #to_email');
    let sendEmailInputVal = $sendEmailInput.val();

    $tabList.on('click', function(event) {
        $tabList.removeClass('active');
        jQuery(this).addClass('active');
        jQuery('.order-price-tabs .tab').removeClass('open');
        jQuery('#' + jQuery(this).data('target')).addClass('open');
    });

    $sendEmailInput.on('keyup', function(event) {
        $sendEmailBtn.removeAttr('disabled');
    });

    $sendEmailInput.on('change', function(event) {
        if ( jQuery(this).val() === sendEmailInputVal ) {
            $sendEmailBtn.attr('disabled', 'on');
        }
    });


});
