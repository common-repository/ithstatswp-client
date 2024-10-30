<?php

defined('ITH_DEFINED') or die('No direct script access.');

class IthListPluginsThemesAction extends BaseAction
{
    public function verbs()
    {
        return 'GET';
    }

    public function access()
    {
        return false;
    }

    public function run($entity)
    {
        $pluginsResponse = $themesResponse = [];

        if (!function_exists('get_plugins'))
            include_once ABSPATH . 'wp-admin/includes/plugin.php';

        $plugins = get_plugins();
        foreach ($plugins as $key => $plugin) {
            $pluginsResponse['plugins'][] = [
                'name' => $plugin['Name'],
                'enkey' => $key,
                'version' => $plugin['Version'],
                'hash' => ITH_Core::hash($key),
                'is_active' => is_plugin_active($key) ? true : false,
            ];
        }

        $themes = wp_get_themes();
        $currentTheme = preg_replace('#\s+#', '', wp_get_theme()->template);
        foreach ($themes as $key => $theme) {
            $themesResponse['themes'][] = [
                'name' => $theme->Name,
                'enkey' => $key,
                'version' => $theme->Version,
                'hash' => ITH_Core::hash($theme->Name),
                'is_active' => strcasecmp($currentTheme, $key) == 0 ? true : false,
            ];
        }

        return array_merge($pluginsResponse, $themesResponse);
    }
}