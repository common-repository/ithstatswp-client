<?php

defined('ITH_DEFINED') or die('No direct script access.');

if (!class_exists('ITH_Bulk_Theme')) {
    class ITH_Bulk_Theme extends Bulk_Theme_Upgrader_Skin
    {
        public function flush_output()
        {

        }
    }
}