<?php
/**
 * Plugin Name: Download Image Extension
 * Plugin URI: https://github.com/Zrgisa/DownloadImagePlugin
 * Description: An extension for image downloads inside the Masonry Gallery block.
 * Version: 0.1
 * Author: GoDaddy
 * Text Domain: download-image
 */

function download_image_enqueue() {
    wp_enqueue_script(
        'download-image-script',
        plugins_url( 'download-image.js', __FILE__ ),
        array( 'wp-blocks', 'wp-dom-ready', 'wp-edit-post', 'lodash' ),
        filemtime( plugin_dir_path( __FILE__ ) . '/download-image.js' )
    );
}

add_action( 'enqueue_block_editor_assets', 'download_image_enqueue' );
