jQuery(document).ready(function() {

    const loaderHtml = `
    <div id="fountainG">
        <div id="fountainG_1" class="fountainG"></div>
        <div id="fountainG_2" class="fountainG"></div>
        <div id="fountainG_3" class="fountainG"></div>
        <div id="fountainG_4" class="fountainG"></div>
        <div id="fountainG_5" class="fountainG"></div>
        <div id="fountainG_6" class="fountainG"></div>
        <div id="fountainG_7" class="fountainG"></div>
        <div id="fountainG_8" class="fountainG"></div>
    </div>
    `;
    let $button     = jQuery('#order_price .action_btn');
    let $message    = jQuery('#order_price .message');
    let $form       = jQuery('#order_price .form form');
    let $sendButton = jQuery('#order_price .send_form_btn');
    
    $form.on('submit', function(event) {
        event.preventDefault();
        $sendButton.html(loaderHtml);
        let data         = jQuery(this).serialize();
        let wpAjaxAction = jQuery(this).attr('action');
        jQuery.ajax({
            url: ajax_url,
            type: 'POST',
            data: `action=${wpAjaxAction}&email=${data}`,
        })
        .done(function( data ) {
            let response = JSON.parse(data);
            if ( response.status === 'ok' ) {
                $message.text( response.msg );
                $sendButton.html('Запросить прайс');
            } else if ( response.status === 'error' ) {
                $message.text( response.msg );
                $sendButton.html('Запросить прайс');
            }
        })
        .fail(function() {
            alert('К сожалению что-то явно пошло не так, всё пропало! :(');
        });
    });

});

