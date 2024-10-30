<?php
/*
Plugin Name: IthStatsWP client
Plugin URI: http://ithtesting.com
Description: IthStatsWP client.
Version: 0.0.2
Author: 4ebizz
Author URI: http://ithtesting.com
*/

defined('ABSPATH') || exit;

define('PHP_EXT', '.php');

define('ITH_DEFINED', true);

//Версия плагина
defined('ITH_CLIENT_PLUGIN_VERSION') ?: define('ITH_CLIENT', '0.0.2');

if (version_compare(PHP_VERSION, '5.3.0', '<'))
    wp_die('Обновите версию php! Текущая версия: ' . PHP_VERSION);

//Корневая директория плагина
defined('ITH_CLIENT_PLUGIN_URL') ?: define('ITH_CLIENT_PLUGIN_URL', plugin_dir_url(__FILE__));
defined('ITH_CLIENT_PLUGIN_DIR') ?: define('ITH_CLIENT_PLUGIN_DIR', plugin_dir_path(__FILE__));

spl_autoload_register('ith_autoload');

function ith_autoload($class)
{
    if (file_exists(ITH_CLIENT_PLUGIN_DIR . 'includes/' . $class . PHP_EXT))
        include ITH_CLIENT_PLUGIN_DIR . 'includes/' . $class . PHP_EXT;

    if (file_exists(ITH_CLIENT_PLUGIN_DIR . 'includes/actions/' . $class . PHP_EXT))
        include ITH_CLIENT_PLUGIN_DIR . 'includes/actions/' . $class . PHP_EXT;

    if (file_exists(ITH_CLIENT_PLUGIN_DIR . 'includes/base/' . $class . PHP_EXT))
        include ITH_CLIENT_PLUGIN_DIR . 'includes/base/' . $class . PHP_EXT;
}

if (function_exists('add_action')) {
    add_action('setup_theme', ['ITH_Core', 'ith_request']);

    if (is_admin() && array_key_exists('ith_admin_notice', $_COOKIE) && $_COOKIE['ith_admin_notice']) {
        add_action('admin_enqueue_scripts', 'ith_styles');
        add_action('admin_notices', 'admin_notice');
    }
}

register_activation_hook(__FILE__, 'ith_install');
register_deactivation_hook(__FILE__, 'ith_uninstall');

if (!function_exists('ith_install')) {
    function ith_install()
    {
        if (get_option('ith_auth_key'))
            delete_option('ith_auth_key');

        add_option('ith_auth_key', md5(rand(10000, 99999) . '~' . wp_generate_password() . '~' . get_option('siteurl')));

        if (setcookie('ith_admin_notice', true, time() + 3600 * 24, '/', '.' . $_SERVER['HTTP_HOST'])) {
            add_action('admin_enqueue_scripts', 'ith_styles');
            add_action('admin_notices', 'admin_notice');
        }
    }
}

if (!function_exists('ith_uninstall')) {
    function ith_uninstall()
    {
        delete_option('ith_auth_key');

        if (array_key_exists('ith_admin_notice', $_COOKIE))
            unset($_COOKIE['ith_admin_notice']);
    }
}

function ith_styles()
{
    wp_enqueue_style('styles', ITH_CLIENT_PLUGIN_URL . 'assets/css/styles.css');
}

function admin_notice()
{
    $current_user = wp_get_current_user();

    echo str_replace([
        '{{site_url}}', '{{admin_username}}', '{{admin_auth_key}}',
    ], [
        site_url(), $current_user->data->user_login, get_option('ith_auth_key'),
    ], file_get_contents(ITH_CLIENT_PLUGIN_DIR . 'assets/templates/notice.tpl'));
}