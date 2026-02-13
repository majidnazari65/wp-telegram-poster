<?php
/*
Plugin Name: WP Telegram Poster
Description: Adds a button to publish WordPress posts to Telegram via a Cloudflare Worker
Version: 1.0
Author: AnilTarah.com
Domain Path: /languages
*/

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Load translations
function wptp_load_textdomain() {
    load_plugin_textdomain('wp-telegram-poster', false, dirname(plugin_basename(__FILE__)) . '/languages/');
}
add_action('plugins_loaded', 'wptp_load_textdomain');

// Add button to publish metabox for public post types
function wptp_add_telegram_button() {
    global $post;
    $post_type = get_post_type_object($post->post_type);
    if ($post_type && $post_type->public) {
        echo '<div id="telegram-button-wrapper" class="misc-pub-section"><button type="button" id="send-to-telegram" class="button">' . esc_html__('Send to Telegram', 'wp-telegram-poster') . '</button></div>';
    }
}
add_action('post_submitbox_misc_actions', 'wptp_add_telegram_button');

// Enqueue JavaScript
function wptp_enqueue_telegram_script($hook) {

    if ($hook == 'post.php' || $hook == 'post-new.php') {
        // اضافه کردن wp-edit-post و wp-element برای پشتیبانی از گوتنبرگ
        wp_enqueue_script('wptp-telegram-script', plugin_dir_url(__FILE__) . 'js/telegram.js', array('jquery', 'wp-edit-post', 'wp-element', 'wp-components', 'wp-plugins'), '1.3', true);
        
        wp_localize_script('wptp-telegram-script', 'telegram_vars', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'post_id' => get_the_ID(),
            'button_text' => esc_html__('Send to Telegram', 'wp-telegram-poster')
        ));
    }
}
add_action('admin_enqueue_scripts', 'wptp_enqueue_telegram_script');

// AJAX handler to send data to Cloudflare Worker
function wptp_send_to_telegram() {
    $post_id = intval($_POST['post_id']);
    if (!current_user_can('edit_post', $post_id)) {
        wp_send_json_error(__('Unauthorized', 'wp-telegram-poster'));
    }
    $post = get_post($post_id);
    if (!$post || $post->post_status != 'publish') {
        wp_send_json_error(__('Post is not published', 'wp-telegram-poster'));
    }


    // Gather post data
    $title = $post->post_title;
    $excerpt = get_the_excerpt($post);
    $permalink = get_permalink($post);
    $featured_image = has_post_thumbnail($post) ? wp_get_attachment_url(get_post_thumbnail_id($post)) : '';

    // Get all taxonomies for the post type
    $taxonomies = get_object_taxonomies($post->post_type, 'objects');
    $hashtags = array();
    $postType = get_post_type_object(get_post_type($post));
    if ($postType && $postType->name!='post' && $postType->name!='page') {
        $post_type_name = esc_html($postType->labels->singular_name);
        $hashtag =  prepare_hashtag_text_for_telegram($post_type_name);
        if ($hashtag !== '#') {
            $hashtags[]=$hashtag;
        }
    }
    foreach ($taxonomies as $taxonomy) {
        if ($taxonomy->public) { // Only public taxonomies
            $terms = get_the_terms($post_id, $taxonomy->name);
            if ($terms && !is_wp_error($terms)) {
                foreach ($terms as $term) {
                    // Convert term name to hashtag: replace special chars with _, keep Unicode letters and numbers
                    $hashtag =  prepare_hashtag_text_for_telegram($term->name);
                    // Skip empty hashtags
                    if ($hashtag !== '#') {
                        $hashtags[] = $hashtag;
                    }
                }
            }
        }
    }
    // Remove duplicates
    $hashtags = array_unique($hashtags);

    // Get settings
    $secret = get_option('wptp_secret_key', '');
    $worker_url = get_option('wptp_worker_url', '');
    $chat_id = get_option('wptp_chat_id', '');
    $read_more_text = get_option('wptp_read_more_text', 'Read more');

    if (empty($secret) || empty($worker_url) || empty($chat_id)) {
        wp_send_json_error(__('Plugin settings not configured', 'wp-telegram-poster'));
    }

    // Prepare JSON data
    $data = array(
        'secret' => $secret,
        'title' => $title,
        'excerpt' => $excerpt,
        'permalink' => $permalink,
        'featured_image' => $featured_image,
        'chat_id' => $chat_id,
        'read_more_text' => $read_more_text,
        'hashtags' => $hashtags,
    );

    // Send to Cloudflare Worker
    $response = wp_remote_post($worker_url, array(
        'body' => json_encode($data),
        'headers' => array('Content-Type' => 'application/json'),
    ));

    if (is_wp_error($response)) {
        wp_send_json_error(__('Failed to send to worker: ', 'wp-telegram-poster') . $response->get_error_message());
    } else {
        $status_code = wp_remote_retrieve_response_code($response);
        if ($status_code == 200) {
            wp_send_json_success(__('Sent to Telegram via worker', 'wp-telegram-poster'));
        } else {
            wp_send_json_error(__('Worker error: ', 'wp-telegram-poster') . $status_code);
        }
    }
}
add_action('wp_ajax_wptp_send_to_telegram', 'wptp_send_to_telegram');

function prepare_hashtag_text_for_telegram($hashtag){
    // Convert term name to hashtag: replace special chars with _, keep Unicode letters and numbers
    $hashtag = '#' . preg_replace('/[^\p{L}\p{N}]/u', '_', $hashtag);
    // Remove multiple consecutive underscores and trim
    $hashtag = preg_replace('/_+/', '_', $hashtag);
    $hashtag = trim($hashtag, '_');
    // Escape Markdown characters (_ and *) for Telegram
    $hashtag = str_replace('_', '\_', $hashtag);
    $hashtag = str_replace('*', '\*', $hashtag);
    return $hashtag;
}

// Add settings page
function wptp_add_settings_page() {
    add_options_page(
        __('WP Telegram Poster Settings', 'wp-telegram-poster'),
        __('WP Telegram Poster', 'wp-telegram-poster'),
        'manage_options',
        'wptp-settings',
        'wptp_render_settings_page'
    );
}
add_action('admin_menu', 'wptp_add_settings_page');

// Render settings page
function wptp_render_settings_page() {
    ?>
    <div class="wrap">
        <h1><?php echo esc_html__('WP Telegram Poster Settings', 'wp-telegram-poster'); ?></h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('wptp_settings_group');
            do_settings_sections('wptp-settings');
            submit_button();
            ?>
        </form>
    </div>
    <?php
}

// Register settings
function wptp_register_settings() {
    register_setting('wptp_settings_group', 'wptp_secret_key', 'sanitize_text_field');
    register_setting('wptp_settings_group', 'wptp_worker_url', 'esc_url_raw');
    register_setting('wptp_settings_group', 'wptp_chat_id', 'sanitize_text_field');
    register_setting('wptp_settings_group', 'wptp_read_more_text', 'sanitize_text_field');

    add_settings_section(
        'wptp_main_section',
        __('Configuration', 'wp-telegram-poster'),
        'wptp_section_callback',
        'wptp-settings'
    );

    add_settings_field(
        'wptp_secret_key',
        __('Secret Key', 'wp-telegram-poster'),
        'wptp_secret_key_callback',
        'wptp-settings',
        'wptp_main_section'
    );

    add_settings_field(
        'wptp_worker_url',
        __('Cloudflare Worker URL', 'wp-telegram-poster'),
        'wptp_worker_url_callback',
        'wptp-settings',
        'wptp_main_section'
    );

    add_settings_field(
        'wptp_chat_id',
        __('Telegram Chat ID', 'wp-telegram-poster'),
        'wptp_chat_id_callback',
        'wptp-settings',
        'wptp_main_section'
    );

    add_settings_field(
        'wptp_read_more_text',
        __('Read More Button Text', 'wp-telegram-poster'),
        'wptp_read_more_text_callback',
        'wptp-settings',
        'wptp_main_section'
    );
}
add_action('admin_init', 'wptp_register_settings');

// Section callback
function wptp_section_callback() {
    echo '<p>' . esc_html__('Enter the secret key, Cloudflare Worker URL, Telegram Chat ID, and Read More button text to connect WordPress to your Telegram bot.', 'wp-telegram-poster') . '</p>';
}

// Field callbacks
function wptp_secret_key_callback() {
    $value = esc_attr(get_option('wptp_secret_key', ''));
    echo "<input type='text' name='wptp_secret_key' value='$value' class='regular-text' />";
    echo '<p class="description">' . esc_html__('Enter the secret key. this secret key must used in cludflare worker envirounment valiables.', 'wp-telegram-poster') . '</p>';
}

function wptp_worker_url_callback() {
    $value = esc_attr(get_option('wptp_worker_url', ''));
    echo "<input type='url' name='wptp_worker_url' value='$value' class='regular-text' />";
    echo '<p class="description">' . esc_html__('Enter the cloudflare worker url.', 'wp-telegram-poster') . '</p>';
}

function wptp_chat_id_callback() {
    $value = esc_attr(get_option('wptp_chat_id', ''));
    echo "<input type='text' name='wptp_chat_id' value='$value' class='regular-text' />";
    echo '<p class="description">' . esc_html__('Enter the Telegram Chat ID (e.g., @YourChannel or a group ID).', 'wp-telegram-poster') . '</p>';
}


function wptp_read_more_text_callback() {
    $value = esc_attr(get_option('wptp_read_more_text', 'Read more'));
    echo "<input type='text' name='wptp_read_more_text' value='$value' class='regular-text' />";
    echo '<p class="description">' . esc_html__('Text for the Read More button in Telegram messages.', 'wp-telegram-poster') . '</p>';
}
?>