<?php

defined('ITH_DEFINED') or die('No direct script access.');

class IthUpgradeThemesAction extends BaseAction
{
    public function run($entity)
    {
        $data = $update_themes_previous = [];

        include_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
        include_once ABSPATH . 'wp-admin/includes/file.php';

        if (class_exists('Theme_Upgrader') && class_exists('Bulk_Theme_Upgrader_Skin')) {
            $update_themes = get_site_transient('update_themes');
            if ($update_themes && property_exists($update_themes, 'response')) {
                if (!property_exists($update_themes, 'checked')) {
                    @wp_update_themes();
                    $update_themes = get_site_transient('update_themes');
                }

                //Текущие версии тем
                $checked = $update_themes->checked;

                //Все темы
                $themes = $update_themes->response;

                foreach ($themes as $theme => $object) {
                    if (array_key_exists($theme, $checked))
                        $update_themes_previous[$theme] = $checked[$theme];
                }

                if ($entity)
                    $update_themes_previous = array_intersect_key($update_themes_previous, array_flip([$entity]));

                $upgrader = new Theme_Upgrader(new ITH_Bulk_Theme(compact('title', 'nonce', 'url', 'theme')));
                $result = $upgrader->bulk_upgrade(array_keys($update_themes_previous));

                @wp_update_themes();
                $update_themes_current = get_site_transient('update_themes');

                if (!empty($result)) {
                    foreach ($result as $key => $value) {
                        if (!$value || is_wp_error($value)) {
                            $data[$key] = $this->prepareResult(['status' => 0, 'msg' => 'Failed to update the plugin!']);
                        } else {
                            if ($result[$key] || version_compare($update_themes_previous[$key], $update_themes_current->checked[$key], '<')) {
                                $data[$key] = $this->prepareResult(['status' => 200, 'msg' => 'Theme successfully upgraded!']);
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