<?php
/*
Plugin Name: Fix WP Inject
Plugin URI: http://elhardoum.com/work-with-me
Description: Fix WP Inject Flickr integration.
Author: Samuel Elh
Version: 0.1
Author URI: http://elhardoum.com/work-with-me
License: GPLv3
*/

if ( ! defined ( 'WPINC' ) ) {
    exit; // direct access
}

add_action('plugins_loaded', function()
{
    add_action('wp_ajax_wpdf_save_keys', 'wpdf_editor_ajax_save_keys_function');

    in_array($GLOBALS['pagenow'], ['post.php', 'post-new.php']) && add_action('admin_enqueue_scripts', function()
    {
        wp_enqueue_script('fix-wp-inject', trailingslashit(plugin_dir_url(__FILE__)) . 'admin.js', ['jquery']);

        $opt = (array) get_option('wpinject_settings');
        if ( ! isset( $opt['flickr']['options']['appid']['value'] ) || ! $opt['flickr']['options']['appid']['value'] ) {
            ob_start(); ?>
            <div id="wpdf_save_keys_form">
                <p><?php _e("To start injecting images please enter your Flickr API key below:","wpinject") ?></p>
                <label for="flickr_appid">Flickr API Key: <input type="text" value="" id="flickr_appid" name="flickr_appid" class="regular-text"></label><br/>
                <p><?php _e('For more settings head to the <a href="/wp-admin/options-general.php?page=wpdf-options">ImageInject Options</a> page.',"wpinject") ?></p>
                <p><input type="submit" value="Save" id="wpdf_save_keys" name="wpdf_save_keys" class="button-primary"></p>
            </div>
            <?php $html = ob_get_clean();

            wp_localize_script('fix-wp-inject', 'FLICKR_APP_ID_HTML', $html);
        }
    });

    add_filter('option_wpinject_settings', function($value)
    {
        if ( is_array($value) && isset( $value['flickr'] ) && is_array($value['flickr']) ) {
            $value['flickr']['enabled'] = 1;
        }

        return $value;
    });
});

// copied from plugin source
if ( ! function_exists('wpdf_editor_ajax_save_keys_function') ) :
    function wpdf_editor_ajax_save_keys_function() {

        $flickrapi = $_POST["flickrapi"];
        
        $nonce = $_POST["wpnonce"];
        if (!wp_verify_nonce($nonce, 'wpdf_security_nonce')) {
            echo json_encode(array("error" => "Invalid request."));
            exit;
        }

        $options = get_option("wpinject_settings");
        $options["flickr"]["options"]["appid"]["value"] = $flickrapi;
        update_option("wpinject_settings", $options);

        echo json_encode(array("success" => "true"));
        exit;   
    }
endif;