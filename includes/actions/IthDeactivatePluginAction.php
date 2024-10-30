<?php

defined('ITH_DEFINED') or die('No direct script access.');

class IthDeactivatePluginAction extends BaseAction
{
    public function run($entity)
    {
        if (!function_exists('is_plugin_active'))
            include_once(ABSPATH . 'wp-admin/includes/plugin.php');

        if (is_plugin_active($entity)) {
            deactivate_plugins($entity);
            $data[$entity] = $this->prepareResult(['status' => 200, 'msg' => 'Plugin successfully deactivated!']);
        } else {
            $data[$entity] = $this->prepareResult(['status' => 0, 'msg' => 'Plugin is not activated!']);
        }

        ob_end_clean();
        return $data;
    }

}