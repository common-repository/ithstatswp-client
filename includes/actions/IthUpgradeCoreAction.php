<?php

defined('ITH_DEFINED') or die('No direct script access.');

class IthUpgradeCoreAction extends BaseAction
{
    public function run($entity)
    {
        global $wp_version;

        @wp_version_check();

        $data = [];

        $update_core = get_site_transient('update_core');
        if ($update_core && property_exists($update_core, 'updates')) {
            $updates = $update_core->updates[0];

            if (property_exists($updates, 'response') && $updates->response == 'latest') {
                return $this->prepareResult(['upgrade' => false]);
            }

            if (version_compare($wp_version, $updates->current, '<')) {

                include_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
                include_once ABSPATH . 'wp-admin/includes/update.php';
                include_once ABSPATH . 'wp-admin/includes/file.php';
                include_once ABSPATH . 'wp-admin/includes/misc.php';

                WP_Filesystem();

                if (version_compare($wp_version, '3.1.9', '>')) {
                    $this->ith_maintenance_mode(true);

                    $core = new Core_Upgrader(new ITH_Upgrader_Skins());
                    $result = $core->upgrade($updates);

                    $data = is_wp_error($result)
                        ? $this->prepareResult(['status' => 0, 'msg' => $result])
                        : $this->prepareResult(['status' => 200, 'msg' => 'Core successfully upgraded!']);
                } else {
                    if (!class_exists('WP_Upgrader')) {
                        if (function_exists('wp_update_core')) {
                            $this->ith_maintenance_mode(true);
                            $result = wp_update_core($update_core->updates);

                            $data = is_wp_error($result)
                                ? $this->prepareResult(['status' => 0, 'msg' => $result])
                                : $this->prepareResult(['status' => 200, 'msg' => 'Core successfully upgraded!']);
                        }
                    }
                }

                $this->ith_maintenance_mode();
            }
        }

        ob_end_clean();
        return $data;
    }

    /**
     * @param bool|false $enable
     * @param string $maintenance_message
     */
    protected function ith_maintenance_mode($enable = false, $maintenance_message = '')
    {
        global $wp_filesystem;

        $maintenance_message .= '<?php $upgrading = ' . time() . '; ?>';

        $file = $wp_filesystem->abspath() . '.maintenance';
        if ($enable) {
            $wp_filesystem->delete($file);
            $wp_filesystem->put_contents($file, $maintenance_message, FS_CHMOD_FILE);
        } else {
            $wp_filesystem->delete($file);
        }
    }

}