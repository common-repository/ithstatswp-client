<?php

defined('ITH_DEFINED') or die('No direct script access.');

if (!class_exists('ITH_Auth')) {
    class ITH_Auth
    {
        /**
         * @param $auth_key
         * @return bool
         */
        public static function authenticate($auth_key)
        {
            if (!get_option('ith_auth_key'))
                return false;

            if (strcmp(get_option('ith_auth_key'), $auth_key) == 0)
                return true;

            return false;
        }
    }
}