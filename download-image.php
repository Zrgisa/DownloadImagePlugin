<?php
/**
 * Plugin Name: Download Image Extension
 * Plugin URI: https://github.com/Zrgisa/DownloadImagePlugin
 * Description: An extension for image downloads inside the Masonry Gallery block.
 * Version: 0.1
 * Author: GoDaddy
 * Text Domain: download-image
 */

function enqueue_download_image_editor_extension()
{
    wp_enqueue_script(
        'download-image-script',
        plugins_url('download-image.js', __FILE__),
        array('wp-blocks', 'wp-dom-ready', 'wp-edit-post', 'lodash'),
        filemtime(plugin_dir_path(__FILE__).'/download-image.js')
    );
}

function enqueue_download_image_css()
{
    wp_enqueue_style(
        'download-image-style',
        plugins_url('download-image.css', __FILE__),
        array(),
        '0.1'
    );
}

function do_redirect_to_file($hash, $filename)
{
    wp_redirect(wp_upload_dir()['baseurl'].'/download-image-archives/'.$hash.'/'.$filename);
    exit();
}

function download_collection_api()
{
    $param = isset($_GET['download-image-collection']) ? $_GET['download-image-collection'] : null;
    $name  = isset($_GET['name']) ? $_GET['name'] : 'images';

    if (empty($param)) {
        return;
    }

    $uploadDir = wp_upload_dir()['basedir'].'/download-image-archives';

    if (!file_exists($uploadDir)) {
        mkdir($uploadDir);
    }

    $hashDir  = md5($param);
    $hashPath = $uploadDir.'/'.$hashDir;

    if (!file_exists($hashPath)) {
        mkdir($hashPath);
    }

    $filename = $name.'.zip';
    $path     = $hashPath.'/'.$filename;

    if (file_exists($path)) {
        do_redirect_to_file($hashDir, $filename);
        return;
    }

    $ids = explode('-', $param);
    if (count($ids) < 1) {
        return;
    }

    $zip = new ZipArchive();
    $zip->open($path, ZipArchive::CREATE);

    foreach ($ids as $id) {
        $path = get_attached_file($id);

        if ($path === false) {
            continue;
        }

        $zip->addFile($path, basename($path));
    }

    $zip->close();

    do_redirect_to_file($hashDir, $filename);
}

add_action('enqueue_block_editor_assets', 'enqueue_download_image_editor_extension');
add_action('wp_enqueue_scripts', 'enqueue_download_image_css');
add_action('wp_loaded', 'download_collection_api');
