(function (wp, $) {
    if (!wp || !wp.editPost) return;

    const { registerPlugin } = wp.plugins;
    const { PluginPostStatusInfo } = wp.editPost;
    const { Button } = wp.components;

    const TelegramButton = () => {
        return wp.element.createElement(
            PluginPostStatusInfo,
            {},
            wp.element.createElement(
                Button,
                {
                    isSecondary: true,
                    className: 'button-primary', // استایل شبیه دکمه وردپرس
                    onClick: function() {
                        // فراخوانی فانکشن قدیمی شما که عملیات AJAX را انجام می‌دهد
                        if (typeof window.sendTelegramAjax === 'function') {
                            window.sendTelegramAjax();
                        }
                    },
                    style: { width: '100%', justifyContent: 'center', marginTop: '10px' }
                },
                telegram_vars.button_text
            )
        );
    };

    registerPlugin('wp-telegram-poster-button', {
        render: TelegramButton,
    });
})(window.wp, window.jQuery);

// فانکشن کمکی برای اجرای درخواست AJAX (اگر قبلاً ننوشته‌اید)
window.sendTelegramAjax = function() {
    const $button = jQuery('#send-to-telegram'); // برای کلاسیک
    // در گوتنبرگ می‌توان مستقیم با telegram_vars.post_id کار کرد
    
    jQuery.post(telegram_vars.ajax_url, {
        action: 'wptp_send_to_telegram',
        post_id: telegram_vars.post_id
    }, function(response) {
        if (response.success) {
            alert(response.data);
        } else {
            alert('Error: ' + response.data);
        }
    });
};

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