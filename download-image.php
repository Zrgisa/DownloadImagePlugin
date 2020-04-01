<?php
/**
 * Plugin Name: Download Image Extension
 * Plugin URI: https://github.com/Zrgisa/DownloadImagePlugin
 * Description: An extension for image downloads inside the Masonry Gallery block.
 * Version: 0.1
 * Author: GoDaddy
 * Text Domain: download-image
 */

function enqueue_download_image_editor_extension() {
    wp_enqueue_script(
        'download-image-script',
        plugins_url( 'download-image.js', __FILE__ ),
        array( 'wp-blocks', 'wp-dom-ready', 'wp-edit-post', 'lodash' ),
        filemtime( plugin_dir_path( __FILE__ ) . '/download-image.js' )
    );
}

function enqueue_download_image_css()
{
    wp_enqueue_style(
        'download-image-style',
        plugins_url( 'download-image.css', __FILE__ ),
        array(),
        '0.1'
    );
}

add_action( 'enqueue_block_editor_assets', 'enqueue_download_image_editor_extension' );
add_action( 'wp_enqueue_scripts', 'enqueue_download_image_css' );
