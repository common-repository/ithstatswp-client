<?php

defined('ITH_DEFINED') or die('No direct script access.');

class IthAuthenticateAction extends BaseAction
{
    public function verbs()
    {
        return 'GET';
    }

    public function run($entity)
    {
        if (!function_exists('is_user_logged_in'))
            include_once(ABSPATH . 'wp-includes/pluggable.php');

        if (is_user_logged_in())
            $this->redirect();

        $user = $this->getUser();
        wp_set_current_user($user->ID);
        wp_set_auth_cookie($user->ID);

        $this->redirect();
    }

    protected function redirect()
    {
        if (function_exists('wp_safe_redirect') && function_exists('admin_url')) {
            wp_safe_redirect(admin_url('/'));
            exit();
        }
    }

    /**
     * @return bool|false|object|WP_User
     */
    protected function getUser()
    {
        global $wp_version;

        if (version_compare($wp_version, '3.2.2', '<=')) {
            return get_userdatabylogin($_GET['ith_username']);
        } else {
            return get_user_by('login', $_GET['ith_username']);
        }
    }
}