<?php

defined('ITH_DEFINED') or die('No direct script access.');

class IthUpgradePluginsAction extends BaseAction
{

    public function run($entity)
    {
        $data = $update_plugins_previous = [];

        include_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
        include_once ABSPATH . 'wp-admin/includes/file.php';
        include_once ABSPATH . 'wp-admin/includes/plugin.php';

        if (class_exists('Plugin_Upgrader') && class_exists('Bulk_Plugin_Upgrader_Skin')) {
            $update_plugins = get_site_transient('update_plugins');
            if ($update_plugins && property_exists($update_plugins, 'response')) {
                if (!property_exists($update_plugins, 'checked')) {
                    @wp_update_plugins();
                    $update_plugins = get_site_transient('update_plugins');
                }

                //Текущие версии плагинов
                $checked = $update_plugins->checked;

                //Все плагины
                $plugins = $update_plugins->response;

                foreach ($plugins as $plugin => $object) {
                    if (array_key_exists($plugin, $checked))
                        $update_plugins_previous[$plugin] = $checked[$plugin];
                }

                if ($entity)
                   $update_plugins_previous = array_intersect_key($update_plugins_previous, array_flip([$entity]));

                $upgrader = new Plugin_Upgrader(new ITH_Bulk_Plugin(compact('nonce', 'url')));
                $result = $upgrader->bulk_upgrade(array_keys($update_plugins_previous));

                @wp_update_plugins();
                $update_plugins_current = get_site_transient('update_plugins');

                if (!empty($result)) {
                    foreach ($result as $key => $value) {
                        if (!$value || is_wp_error($value)) {
                            $data[$key] = $this->prepareResult(['status' => 0, 'msg' => 'Failed to update the plugin!']);
                        } else {
                            if ($result[$key] || version_compare($update_plugins_previous[$key], $update_plugins_current->checked[$key], '<')) {
                                $data[$key] = $this->prepareResult(['status' => 200, 'msg' => 'Plugin successfully upgraded!']);
                            } else {
                                $data[$key] = $this->prepareResult(['status' => 0, 'msg' => 'Could not refresh upgrade transients, please reload website data!']);
                            }
                        }
                    }
                } else {
                    $data = $this->prepareResult(['status' => 0, 'msg' => 'Upgrade failed!']);
                }
            }
        } else {
            $data = $this->prepareResult(['status' => 0, 'msg' => 'WordPress update required first!']);
        }

        ob_end_clean();
        return $data;
    }
}