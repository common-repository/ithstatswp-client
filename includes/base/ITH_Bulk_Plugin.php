<?php

defined('ITH_DEFINED') or die('No direct script access.');

if (!class_exists('ITH_Bulk_Plugin')) {
    class ITH_Bulk_Plugin extends Bulk_Plugin_Upgrader_Skin
    {
        public function flush_output()
        {

        }
    }
}