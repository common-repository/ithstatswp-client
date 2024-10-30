<?php

defined('ITH_DEFINED') or die('No direct script access.');

class IthCheckUpgradeAction extends BaseAction
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
        global $wp_version;

        @wp_version_check();
        @wp_update_themes();
        @wp_update_plugins();

        $data = [];

        //Проверка плагинов
        if ($update_plugins = get_site_transient('update_plugins')) {
            $versions_plugins = $this->versionCompare($update_plugins);
            $data['plugins'] = [
                'last_checked' => $update_plugins->last_checked,
                'upgrade' => sizeof($versions_plugins) > 0 ? $versions_plugins : [],
            ];
        }

        //Проверка тем
        if ($update_themes = get_site_transient('update_themes')) {
            $versions_themes = $this->versionCompare($update_themes);
            $data['themes'] = [
                'last_checked' => $update_themes->last_checked,
                'upgrade' => sizeof($versions_themes) > 0 ? $versions_themes : [],
            ];
        }

        //Проверка ядра
        $update_core = get_site_transient('update_core');
        if ($update_core && property_exists($update_core, 'updates')) {
            $updates = $update_core->updates;
            if (version_compare($wp_version, $updates[0]->current, '<')) {
                $data['core'] = [
                    'upgrade' => true,
                    'old_version' => $wp_version,
                    'new_version' => $updates[0]->current,
                ];
            } else {
                $data['core'] = [
                    'upgrade' => false,
                    'old_version' => $wp_version,
                    'new_version' => $wp_version,
                ];
            }
        }

        return $data;
    }

    /**
     * @param $update
     * @return array
     */
    protected function versionCompare($update)
    {
        $keys = [];
        if (property_exists($update, 'checked') && property_exists($update, 'response')) {
            foreach ($update->checked as $key => $value) {
                if (array_key_exists($key, $update->response)) {
                    $response = $update->response;

                    $new_version = is_object($response[$key])
                        ? $response[$key]->new_version
                        : $response[$key]['new_version'];

                    if (version_compare($value, $new_version, '<')) {
                        $keys[] = $this->prepareResult([
                            'name' => $key,
                            'old_version' => $value,
                            'new_version' => $new_version,
                            'hash' => ITH_Core::hash($key),
                        ]);
                    }
                }
            }
        }

        return $keys;
    }
}