<?php
/**
 * Dynamic Form Block Template
 * 
 * @param array $block The block settings and attributes.
 * @param string $content The block inner HTML (empty).
 * @param bool $is_preview True during backend preview.
 * @param int $post_id The post ID this block is saved to.
 */

// Get ACF fields
$form_fields = get_field('form_fields') ?: [];
$submit_text = get_field('submit_button_text') ?: 'Submit';
$success_message = get_field('success_message') ?: 'Thank you!';
$captcha_site_key = get_field('captcha_site_key') ?: '';
$captcha_secret_key = get_field('captcha_secret_key') ?: '';

// Generate unique form ID
$form_id = 'bblocks-form-' . uniqid();
?>

<div class="bblocks-form-wrapper" id="<?php echo esc_attr($form_id); ?>" data-form-id="<?php echo esc_attr($block['id']); ?>">
    <form class="bblocks-dynamic-form" method="post" enctype="multipart/form-data">
        
        <?php foreach ($form_fields as $index => $field): 
            $field_type = $field['field_type'];
            $field_name = sanitize_key($field['field_name']);
            $field_label = esc_html($field['field_label']);
            $required = $field['field_required'] ? 'required' : '';
            $field_id = $form_id . '-' . $field_name;
        ?>
            <div class="form-field form-field--<?php echo esc_attr($field_type); ?>" data-field-index="<?php echo $index; ?>">
                
                <?php if ($field_type !== 'captcha'): ?>
                    <label for="<?php echo esc_attr($field_id); ?>">
                        <?php echo $field_label; ?>
                        <?php if ($required): ?><span class="required">*</span><?php endif; ?>
                    </label>
                <?php endif; ?>

                <?php switch ($field_type):
                    case 'text': ?>
                        <input type="text" 
                               id="<?php echo esc_attr($field_id); ?>" 
                               name="<?php echo esc_attr($field_name); ?>" 
                               class="form-control" 
                               <?php echo $required; ?>>
                        <?php break; ?>

                    <?php case 'email': ?>
                        <input type="email" 
                               id="<?php echo esc_attr($field_id); ?>" 
                               name="<?php echo esc_attr($field_name); ?>" 
                               class="form-control" 
                               <?php echo $required; ?>>
                        <?php break; ?>

                    <?php case 'textarea': ?>
                        <textarea id="<?php echo esc_attr($field_id); ?>" 
                                  name="<?php echo esc_attr($field_name); ?>" 
                                  class="form-control" 
                                  rows="4" 
                                  <?php echo $required; ?>></textarea>
                        <?php break; ?>

                    <?php case 'select': 
                        $options = explode("\n", $field['field_options']);
                    ?>
                        <select id="<?php echo esc_attr($field_id); ?>" 
                                name="<?php echo esc_attr($field_name); ?>" 
                                class="form-control" 
                                <?php echo $required; ?>>
                            <option value="">-- Select --</option>
                            <?php foreach ($options as $option): 
                                list($value, $label) = array_map('trim', explode(':', $option) + ['', '']);
                                if ($value): ?>
                                    <option value="<?php echo esc_attr($value); ?>"><?php echo esc_html($label ?: $value); ?></option>
                                <?php endif; 
                            endforeach; ?>
                        </select>
                        <?php break; ?>

                    <?php case 'checkbox': 
                        $options = explode("\n", $field['field_options']);
                    ?>
                        <div class="checkbox-group">
                            <?php foreach ($options as $option): 
                                list($value, $label) = array_map('trim', explode(':', $option) + ['', '']);
                                if ($value): ?>
                                    <label class="checkbox-label">
                                        <input type="checkbox" 
                                               name="<?php echo esc_attr($field_name); ?>[]" 
                                               value="<?php echo esc_attr($value); ?>">
                                        <?php echo esc_html($label ?: $value); ?>
                                    </label>
                                <?php endif; 
                            endforeach; ?>
                        </div>
                        <?php break; ?>

                    <?php case 'radio': 
                        $options = explode("\n", $field['field_options']);
                    ?>
                        <div class="radio-group">
                            <?php foreach ($options as $option): 
                                list($value, $label) = array_map('trim', explode(':', $option) + ['', '']);
                                if ($value): ?>
                                    <label class="radio-label">
                                        <input type="radio" 
                                               name="<?php echo esc_attr($field_name); ?>" 
                                               value="<?php echo esc_attr($value); ?>" 
                                               <?php echo $required; ?>>
                                        <?php echo esc_html($label ?: $value); ?>
                                    </label>
                                <?php endif; 
                            endforeach; ?>
                        </div>
                        <?php break; ?>

                    <?php case 'file': ?>
                        <input type="file" 
                               id="<?php echo esc_attr($field_id); ?>" 
                               name="<?php echo esc_attr($field_name); ?>" 
                               class="form-control" 
                               <?php echo $required; ?>>
                        <?php break; ?>

                    <?php case 'date': ?>
                        <input type="date" 
                               id="<?php echo esc_attr($field_id); ?>" 
                               name="<?php echo esc_attr($field_name); ?>" 
                               class="form-control" 
                               <?php echo $required; ?>>
                        <?php break; ?>

                    <?php case 'captcha': 
                        $captcha_provider = $field['captcha_type'] ?? 'hcaptcha';
                        $site_key = $captcha_site_key;
                        
                        if ($site_key): 
                            if ($captcha_provider === 'hcaptcha'): ?>
                                <div class="h-captcha" 
                                     data-sitekey="<?php echo esc_attr($site_key); ?>" 
                                     data-theme="light"></div>
                                <script src="https://js.hcaptcha.com/1/api.js" async defer></script>
                            <?php elseif ($captcha_provider === 'recaptcha_v2'): ?>
                                <div class="g-recaptcha" 
                                     data-sitekey="<?php echo esc_attr($site_key); ?>"></div>
                                <script src="https://www.google.com/recaptcha/api.js" async defer></script>
                            <?php elseif ($captcha_provider === 'recaptcha_v3'): ?>
                                <input type="hidden" name="g-recaptcha-response" id="g-recaptcha-response">
                                <script src="https://www.google.com/recaptcha/api.js?render=<?php echo esc_attr($site_key); ?>"></script>
                                <script>
                                    grecaptcha.ready(function() {
                                        grecaptcha.execute('<?php echo esc_js($site_key); ?>', {action: 'submit'}).then(function(token) {
                                            document.getElementById('g-recaptcha-response').value = token;
                                        });
                                    });
                                </script>
                            <?php endif; 
                        endif;
                        break; ?>

                <?php endswitch; ?>
            </div>
        <?php endforeach; ?>

        <div class="form-messages" style="display: none;"></div>

        <button type="submit" class="btn btn-primary submit-button">
            <?php echo esc_html($submit_text); ?>
        </button>

        <input type="hidden" name="bblocks_form_id" value="<?php echo esc_attr($block['id']); ?>">
        <input type="hidden" name="bblocks_captcha_type" value="<?php echo esc_attr($field['captcha_type'] ?? ''); ?>">
        <input type="hidden" name="bblocks_secret_key" value="<?php echo esc_attr($captcha_secret_key); ?>">
    </form>

    <div class="success-message" style="display: none;">
        <?php echo wp_kses_post($success_message); ?>
    </div>
</div>