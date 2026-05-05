<?php
if (!defined('ABSPATH')) { exit; }

if (!function_exists('wp_theme_booking_enabled')) {
    function wp_theme_booking_enabled() {
        return (bool) wp_theme_acf_get('theme_enable_booking_cpt', 'option', 0);
    }
}

if (!function_exists('wp_theme_register_booking_cpt')) {
    function wp_theme_register_booking_cpt() {
        if (!wp_theme_booking_enabled()) {
            return;
        }
        register_post_type('theme_booking', [
            'labels' => [
                'name' => __('Bookings', 'wp-theme'),
                'singular_name' => __('Booking', 'wp-theme'),
            ],
            'public' => false,
            'show_ui' => true,
            'show_in_menu' => false,
            'supports' => ['title'],
            'capability_type' => 'post',
        ]);
    }
}
add_action('init', 'wp_theme_register_booking_cpt', 20);

if (!function_exists('wp_theme_booking_form_shortcode')) {
    function wp_theme_booking_form_shortcode() {
        if (!wp_theme_booking_enabled()) {
            return '';
        }
        $action = esc_url(admin_url('admin-post.php'));
        $booked = get_posts([
            'post_type' => 'theme_booking',
            'post_status' => 'publish',
            'posts_per_page' => 200,
            'meta_key' => '_booking_date',
            'orderby' => 'meta_value',
            'order' => 'ASC',
        ]);
        $badges = [];
        foreach ($booked as $b) {
            $date = get_post_meta($b->ID, '_booking_date', true);
            if ($date) $badges[] = $date;
        }
        ob_start(); ?>
        <div class="wp-theme-booking-wrap">
            <form class="wp-theme-booking-form" method="post" action="<?php echo $action; ?>">
                <input type="hidden" name="action" value="wp_theme_submit_booking">
                <?php wp_nonce_field('wp_theme_submit_booking', 'wp_theme_booking_nonce'); ?>
                <div class="row g-3">
                    <div class="col-12 col-md-6"><label><?php esc_html_e('Meeting date', 'wp-theme'); ?></label><input class="form-control" type="date" name="booking_date" required min="<?php echo esc_attr(date('Y-m-d')); ?>"></div>
                    <div class="col-12 col-md-6"><label><?php esc_html_e('Full name', 'wp-theme'); ?></label><input class="form-control" type="text" name="booking_name" required></div>
                    <div class="col-12 col-md-6"><label><?php esc_html_e('Email', 'wp-theme'); ?></label><input class="form-control" type="email" name="booking_email" required></div>
                    <div class="col-12 col-md-6"><label><?php esc_html_e('Phone', 'wp-theme'); ?></label><input class="form-control" type="text" name="booking_phone"></div>
                    <div class="col-12"><label><?php esc_html_e('Notes', 'wp-theme'); ?></label><textarea class="form-control" name="booking_notes" rows="4"></textarea></div>
                    <div class="col-12"><button class="btn btn-primary" type="submit"><?php esc_html_e('Book meeting', 'wp-theme'); ?></button></div>
                </div>
            </form>
            <?php if ($badges): ?>
                <div class="wp-theme-booking-booked mt-3"><strong><?php esc_html_e('Booked days:', 'wp-theme'); ?></strong> <?php foreach ($badges as $badge) { echo '<span class="badge text-bg-light border me-1">' . esc_html($badge) . '</span>'; } ?></div>
            <?php endif; ?>
        </div>
        <?php return ob_get_clean();
    }
}
add_shortcode('wp_theme_booking_form', 'wp_theme_booking_form_shortcode');

add_action('admin_post_nopriv_wp_theme_submit_booking', 'wp_theme_handle_booking_submission');
add_action('admin_post_wp_theme_submit_booking', 'wp_theme_handle_booking_submission');
if (!function_exists('wp_theme_handle_booking_submission')) {
    function wp_theme_handle_booking_submission() {
        if (!wp_theme_booking_enabled()) {
            wp_safe_redirect(home_url('/'));
            exit;
        }
        if (!isset($_POST['wp_theme_booking_nonce']) || !wp_verify_nonce($_POST['wp_theme_booking_nonce'], 'wp_theme_submit_booking')) {
            wp_die('Invalid request');
        }
        $date = sanitize_text_field($_POST['booking_date'] ?? '');
        $name = sanitize_text_field($_POST['booking_name'] ?? '');
        $email = sanitize_email($_POST['booking_email'] ?? '');
        $phone = sanitize_text_field($_POST['booking_phone'] ?? '');
        $notes = sanitize_textarea_field($_POST['booking_notes'] ?? '');
        if (!$date || !$name || !$email) {
            wp_safe_redirect(wp_get_referer() ?: home_url('/'));
            exit;
        }
        $existing = get_posts(['post_type'=>'theme_booking','post_status'=>'publish','posts_per_page'=>1,'meta_key'=>'_booking_date','meta_value'=>$date]);
        if ($existing) {
            wp_safe_redirect(add_query_arg('booking_error', 'date_taken', wp_get_referer() ?: home_url('/')));
            exit;
        }
        $post_id = wp_insert_post([
            'post_type' => 'theme_booking',
            'post_status' => 'publish',
            'post_title' => $name . ' - ' . $date,
        ]);
        if ($post_id) {
            update_post_meta($post_id, '_booking_date', $date);
            update_post_meta($post_id, '_booking_name', $name);
            update_post_meta($post_id, '_booking_email', $email);
            update_post_meta($post_id, '_booking_phone', $phone);
            update_post_meta($post_id, '_booking_notes', $notes);
            update_post_meta($post_id, '_booking_answered', 0);
            update_post_meta($post_id, '_booking_status', 'active');
            $admin_email = get_option('admin_email');
            wp_mail($admin_email, sprintf(__('New booking for %s', 'wp-theme'), $date), "Name: {$name}\nEmail: {$email}\nPhone: {$phone}\nDate: {$date}\n\n{$notes}");
            if (wp_theme_acf_get('theme_booking_auto_reply', 'option', 0)) {
                $subject = wp_theme_acf_get('theme_booking_auto_reply_subject', 'option', __('We received your booking request', 'wp-theme'));
                $message = wp_theme_acf_get('theme_booking_auto_reply_message', 'option', __('Thank you. We will reply soon.', 'wp-theme'));
                wp_mail($email, $subject, wp_strip_all_tags($message));
            }
        }
        wp_safe_redirect(add_query_arg('booking_success', '1', wp_get_referer() ?: home_url('/')));
        exit;
    }
}

if (!function_exists('wp_theme_booking_dashboard_markup')) {
    function wp_theme_booking_dashboard_markup() {
        if (!wp_theme_booking_enabled()) {
            return '<div class="notice notice-info inline"><p>' . esc_html__('Enable Booking CPT in General first.', 'wp-theme') . '</p></div>';
        }
        $bookings = get_posts([
            'post_type' => 'theme_booking',
            'post_status' => 'publish',
            'posts_per_page' => 200,
            'meta_key' => '_booking_date',
            'orderby' => 'meta_value',
            'order' => 'ASC',
        ]);
        $month = (int) current_time('n');
        $year = (int) current_time('Y');
        $first = mktime(0,0,0,$month,1,$year);
        $days = (int) date('t', $first);
        $start = (int) date('N', $first);
        $booked = [];
        foreach ($bookings as $booking) {
            $date = get_post_meta($booking->ID, '_booking_date', true);
            if ($date) $booked[$date][] = $booking;
        }
        ob_start();
        echo '<div class="wp-theme-booking-dashboard">';
        echo '<div style="display:grid;grid-template-columns:1.2fr 1.8fr;gap:20px;align-items:start;">';
        echo '<div><h3>' . esc_html(date_i18n('F Y', $first)) . '</h3><table class="widefat striped"><thead><tr>';
        foreach ([__('Mon','wp-theme'),__('Tue','wp-theme'),__('Wed','wp-theme'),__('Thu','wp-theme'),__('Fri','wp-theme'),__('Sat','wp-theme'),__('Sun','wp-theme')] as $wd) echo '<th>' . esc_html($wd) . '</th>';
        echo '</tr></thead><tbody><tr>';
        for ($i=1; $i<$start; $i++) echo '<td></td>';
        $col = $start;
        for ($day=1; $day<=$days; $day++, $col++) {
            $date = sprintf('%04d-%02d-%02d', $year, $month, $day);
            $count = isset($booked[$date]) ? count($booked[$date]) : 0;
            $style = $count ? ' style="background:#dbeafe;font-weight:700;"' : '';
            echo '<td' . $style . '>' . esc_html((string)$day) . ($count ? '<div style="font-size:11px;opacity:.7;">' . esc_html((string)$count) . '</div>' : '') . '</td>';
            if ($col % 7 === 0 && $day !== $days) echo '</tr><tr>';
        }
        while (($col-1) % 7 !== 0) { echo '<td></td>'; $col++; }
        echo '</tr></tbody></table></div>';
        echo '<div><h3>' . esc_html__('Client bookings', 'wp-theme') . '</h3><table class="widefat striped"><thead><tr><th>' . esc_html__('Date','wp-theme') . '</th><th>' . esc_html__('Client','wp-theme') . '</th><th>' . esc_html__('Contact','wp-theme') . '</th><th>' . esc_html__('Notes','wp-theme') . '</th><th>' . esc_html__('Reply','wp-theme') . '</th></tr></thead><tbody>';
        if (!$bookings) {
            echo '<tr><td colspan="5">' . esc_html__('No bookings yet.', 'wp-theme') . '</td></tr>';
        }
        foreach ($bookings as $booking) {
            $id = $booking->ID;
            $date = get_post_meta($id, '_booking_date', true);
            $name = get_post_meta($id, '_booking_name', true);
            $email = get_post_meta($id, '_booking_email', true);
            $phone = get_post_meta($id, '_booking_phone', true);
            $notes = get_post_meta($id, '_booking_notes', true);
            $answered = (int) get_post_meta($id, '_booking_answered', true);
            $status = get_post_meta($id, '_booking_status', true) ?: 'active';
            echo '<tr>';
            echo '<td>' . esc_html($date) . '</td>';
            echo '<td><strong>' . esc_html($name) . '</strong></td>';
            echo '<td><a href="mailto:' . esc_attr($email) . '">' . esc_html($email) . '</a><br>' . esc_html($phone) . '</td>';
            echo '<td><span class="badge text-bg-' . ($status === 'canceled' ? 'danger' : 'success') . '">' . esc_html(ucfirst($status)) . '</span></td>';
            echo '<td>' . esc_html($notes) . '</td>';
            echo '<td>';
            echo '<form method="post" action="' . esc_url(admin_url('admin-post.php')) . '">';
            wp_nonce_field('wp_theme_reply_booking_' . $id, 'wp_theme_reply_nonce');
            echo '<input type="hidden" name="action" value="wp_theme_reply_booking">';
            echo '<input type="hidden" name="booking_id" value="' . esc_attr((string)$id) . '">';
            echo '<textarea name="reply_message" rows="2" style="width:100%;margin-bottom:6px;" placeholder="' . esc_attr__('Reply to client', 'wp-theme') . '"></textarea>';
            echo '<button class="button button-secondary" type="submit">' . esc_html($answered ? __('Reply again','wp-theme') : __('Send reply','wp-theme')) . '</button>';
            echo '</form>';
            echo '</td>';
            echo '<td><form method="post" action="' . esc_url(admin_url('admin-post.php')) . '" style="display:flex;gap:6px;flex-wrap:wrap;">';
            wp_nonce_field('wp_theme_manage_booking_' . $id, 'wp_theme_manage_nonce');
            echo '<input type="hidden" name="action" value="wp_theme_manage_booking">';
            echo '<input type="hidden" name="booking_id" value="' . esc_attr((string)$id) . '">';
            if ($status !== 'canceled') { echo '<button class="button" name="booking_task" value="cancel" type="submit">' . esc_html__('Cancel','wp-theme') . '</button>'; }
            echo '<button class="button button-link-delete" name="booking_task" value="delete" type="submit">' . esc_html__('Delete','wp-theme') . '</button>';
            echo '</form></td>';
            echo '</tr>';
        }
        echo '</tbody></table></div></div></div>';
        return ob_get_clean();
    }
}

add_action('admin_post_wp_theme_reply_booking', function () {
    if (!current_user_can('edit_theme_options')) wp_die('Not allowed');
    $booking_id = absint($_POST['booking_id'] ?? 0);
    if (!$booking_id || !wp_verify_nonce($_POST['wp_theme_reply_nonce'] ?? '', 'wp_theme_reply_booking_' . $booking_id)) wp_die('Invalid request');
    $email = get_post_meta($booking_id, '_booking_email', true);
    $name = get_post_meta($booking_id, '_booking_name', true);
    $message = sanitize_textarea_field($_POST['reply_message'] ?? '');
    if ($email && $message) {
        wp_mail($email, sprintf(__('Reply about your booking, %s', 'wp-theme'), $name ?: __('client', 'wp-theme')), $message);
        update_post_meta($booking_id, '_booking_answered', 1);
        update_post_meta($booking_id, '_booking_reply_message', $message);
    }
    wp_safe_redirect(wp_get_referer() ?: admin_url('options-general.php?page=wp-theme-settings'));
    exit;
});


if (!function_exists('wp_theme_booking_cleanup_tools_markup')) {
    function wp_theme_booking_cleanup_tools_markup() {
        if (!current_user_can('edit_theme_options')) {
            return '';
        }
        $action = esc_url(admin_url('admin-post.php'));
        ob_start();
        echo '<div class="wp-theme-booking-tools"><strong>' . esc_html__('Cleanup tools', 'wp-theme') . '</strong><div style="margin-top:8px;display:flex;gap:8px;flex-wrap:wrap;">';
        echo '<form method="post" action="' . $action . '">';
        wp_nonce_field('wp_theme_cleanup_bookings', 'wp_theme_cleanup_nonce');
        echo '<input type="hidden" name="action" value="wp_theme_cleanup_bookings"><input type="hidden" name="booking_cleanup_task" value="canceled"><button class="button" type="submit">' . esc_html__('Delete canceled now', 'wp-theme') . '</button></form>';
        echo '<form method="post" action="' . $action . '">';
        wp_nonce_field('wp_theme_cleanup_bookings', 'wp_theme_cleanup_nonce');
        echo '<input type="hidden" name="action" value="wp_theme_cleanup_bookings"><input type="hidden" name="booking_cleanup_task" value="old"><button class="button" type="submit">' . esc_html__('Delete old now', 'wp-theme') . '</button></form>';
        echo '</div><p style="margin-top:8px;opacity:.8;">' . esc_html__('Use the switches above for automatic cleanup, or run manual cleanup from these buttons.', 'wp-theme') . '</p></div>';
        return ob_get_clean();
    }
}

if (!function_exists('wp_theme_cleanup_bookings')) {
    function wp_theme_cleanup_bookings($task = 'auto') {
        if (!wp_theme_booking_enabled()) {
            return 0;
        }
        $deleted = 0;
        if ($task === 'canceled' || ($task === 'auto' && wp_theme_acf_get('theme_booking_delete_canceled', 'option', 0))) {
            $posts = get_posts(['post_type' => 'theme_booking', 'post_status' => 'publish', 'posts_per_page' => 200, 'meta_key' => '_booking_status', 'meta_value' => 'canceled', 'fields' => 'ids']);
            foreach ($posts as $post_id) {
                wp_delete_post($post_id, true);
                $deleted++;
            }
        }
        if ($task === 'old' || ($task === 'auto' && wp_theme_acf_get('theme_booking_delete_old', 'option', 0))) {
            $days = max(1, absint(wp_theme_acf_get('theme_booking_delete_old_days', 'option', 30)));
            $cutoff = date('Y-m-d', strtotime('-' . $days . ' days', current_time('timestamp')));
            $posts = get_posts(['post_type' => 'theme_booking', 'post_status' => 'publish', 'posts_per_page' => 500, 'meta_key' => '_booking_date', 'meta_value' => $cutoff, 'meta_compare' => '<=', 'fields' => 'ids']);
            foreach ($posts as $post_id) {
                wp_delete_post($post_id, true);
                $deleted++;
            }
        }
        return $deleted;
    }
}
add_action('admin_init', function () {
    if (is_admin()) {
        wp_theme_cleanup_bookings('auto');
    }
});

add_action('admin_post_wp_theme_manage_booking', function () {
    if (!current_user_can('edit_theme_options')) wp_die('Not allowed');
    $booking_id = absint($_POST['booking_id'] ?? 0);
    if (!$booking_id || !wp_verify_nonce($_POST['wp_theme_manage_nonce'] ?? '', 'wp_theme_manage_booking_' . $booking_id)) wp_die('Invalid request');
    $task = sanitize_key($_POST['booking_task'] ?? '');
    if ($task === 'cancel') {
        update_post_meta($booking_id, '_booking_status', 'canceled');
    } elseif ($task === 'delete') {
        wp_delete_post($booking_id, true);
    }
    wp_safe_redirect(wp_get_referer() ?: admin_url('options-general.php?page=wp-theme-settings'));
    exit;
});

add_action('admin_post_wp_theme_cleanup_bookings', function () {
    if (!current_user_can('edit_theme_options')) wp_die('Not allowed');
    if (!wp_verify_nonce($_POST['wp_theme_cleanup_nonce'] ?? '', 'wp_theme_cleanup_bookings')) wp_die('Invalid request');
    $task = sanitize_key($_POST['booking_cleanup_task'] ?? '');
    wp_theme_cleanup_bookings($task === 'old' ? 'old' : 'canceled');
    wp_safe_redirect(wp_get_referer() ?: admin_url('options-general.php?page=wp-theme-settings'));
    exit;
});
