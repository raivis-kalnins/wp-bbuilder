(function($) {
    'use strict';

    $(document).ready(function() {
        $('.bblocks-dynamic-form').on('submit', function(e) {
            e.preventDefault();
            
            var $form = $(this);
            var $wrapper = $form.closest('.bblocks-form-wrapper');
            var $messages = $form.find('.form-messages');
            var $submitBtn = $form.find('.submit-button');
            
            // Disable submit button
            $submitBtn.prop('disabled', true).text('Submitting...');
            
            // Collect form data
            var formData = {};
            $form.serializeArray().forEach(function(item) {
                if (formData[item.name]) {
                    if (!Array.isArray(formData[item.name])) {
                        formData[item.name] = [formData[item.name]];
                    }
                    formData[item.name].push(item.value);
                } else {
                    formData[item.name] = item.value;
                }
            });
            
            // Get captcha response
            var captchaResponse = '';
            var captchaType = $form.find('input[name="bblocks_captcha_type"]').val();
            
            if (captchaType === 'hcaptcha') {
                captchaResponse = $form.find('[data-hcaptcha-response]').attr('data-hcaptcha-response');
            } else if (captchaType === 'recaptcha_v2') {
                captchaResponse = $form.find('[name="g-recaptcha-response"]').val();
            } else if (captchaType === 'recaptcha_v3') {
                captchaResponse = $form.find('#g-recaptcha-response').val();
            }
            
            // AJAX submission
            $.ajax({
                url: bblocks_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'bblocks_submit_form',
                    nonce: bblocks_ajax.nonce,
                    form_data: formData,
                    captcha_response: captchaResponse,
                    captcha_type: captchaType,
                    secret_key: $form.find('input[name="bblocks_secret_key"]').val()
                },
                success: function(response) {
                    if (response.success) {
                        $form.hide();
                        $wrapper.find('.success-message').show();
                    } else {
                        $messages.html('<div class="error-message">' + response.data.message + '</div>').show();
                    }
                },
                error: function() {
                    $messages.html('<div class="error-message">An error occurred. Please try again.</div>').show();
                },
                complete: function() {
                    $submitBtn.prop('disabled', false).text('Submit');
                }
            });
        });
    });
})(jQuery);