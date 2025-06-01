jQuery(document).ready(function($) {
    $('#send-to-telegram').on('click', function() {
        var post_id = telegram_vars.post_id;
        $.ajax({
            url: telegram_vars.ajax_url,
            type: 'POST',
            data: {
                action: 'wptp_send_to_telegram',
                post_id: post_id,
            },
            beforeSend:function(){
                $('#send-to-telegram').attr('disabled',true);
            },
            complete: function(){
                $('#send-to-telegram').attr('disabled',false);
            },
            success: function(response) {
                if (response.success) {
                    alert('Sent to Telegram');
                } else {
                    alert('Failed: ' + response.data);
                }
            },
            error: function() {
                alert('AJAX error');
            }
        });
    });
});