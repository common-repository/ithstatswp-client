<?php

defined('ITH_DEFINED') or die('No direct script access.');

if (!class_exists('ITH_Upgrader_Skins')) {
    class ITH_Upgrader_Skins extends WP_Upgrader_Skin
    {
        public function feedback($string)
        {
            if (isset($this->upgrader->strings[$string]))
                $string = $this->upgrader->strings[$string];

            if (strpos($string, '%') !== false) {
                $args = func_get_args();
                $args = array_splice($args, 1);
                if ($args) {
                    $args = array_map('strip_tags', $args);
                    $args = array_map('esc_html', $args);
                    $string = vsprintf($string, $args);
                }
            }
            if (empty($string))
                return;
        }
    }
}