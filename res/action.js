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
    </div>`;
    let $button      = jQuery('#order_price .action_btn');
    let $sendButton  = jQuery('#order_price .send_form_btn');
    let $closeButton = jQuery('#order_price .close_form');
    let $message     = jQuery('#order_price .message');
    let $formWrapper = jQuery('#order_price .form');
    let $form        = jQuery('#order_price .form form');
    let hideTooltipDelay;

    function checkValidJSON(string){
        if (typeof string !== "string"){
            return false;
        }
        try{
            JSON.parse(string);
            return true;
        }
        catch (error){
            return false;
        }
    }

    function showTooltip(text) {
        $message.html(text);
        if ( $message.hasClass('open') ) {
            clearTimeout(hideTooltipDelay);
            hideTooltip(5000);
        }
        if ( !$message.hasClass('open') ) {
            $message.animate({
                opacity: 1,
                bottom: $formWrapper.outerHeight() + 10
            },  500, function(){
                $message.addClass('open');
                hideTooltip(5000);
            });    
        }
    }

    function hideTooltip(delay) {
        hideTooltipDelay = setTimeout( function(){
            $message.animate( { opacity:0, bottom:150 }, 300 );
            $message.removeClass('open');
        }, delay );
    }

    $closeButton.on('click', function(event){
        $button.animate({opacity: 1, left: 0}, 200);
        $formWrapper.animate({
            opacity: 0,
            left: -500
        }, 500);
    });

    $button.on('click', function(event){
        jQuery(this).animate({opacity: 0, left: -30}, 200);
        $formWrapper.animate({
            opacity: 1,
            left: 0
        }, 500);
    });

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
            let response;
            if (checkValidJSON(data)) {
                response = JSON.parse(data);
            } else {
                console.warn('От сервера пришёл не корректный JSON =>', data);
                return false;
            }
            if ( response.status === 'ok' ) {
                showTooltip(response.msg);
                $sendButton.html('Запросить прайс');
            } else if ( response.status === 'error' ) {
                showTooltip(response.msg);
                $sendButton.html('Запросить прайс');
            }
        })
        .fail(function() {
            alert('К сожалению что-то явно пошло не так, всё пропало! :(');
        });
    });

});

