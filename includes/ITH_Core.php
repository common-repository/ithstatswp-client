<?php

defined('ITH_DEFINED') or die('No direct script access.');

if (!class_exists('ITH_Core')) {
    class ITH_Core
    {
        const SUCCESS_CODE = 0;

        /**
         * @return null
         */
        public static function ith_request()
        {
            $superArray = strcasecmp($_SERVER['REQUEST_METHOD'], 'GET') == 0 ? $_GET : $_POST;

            if (array_key_exists('ith_action', $superArray)) {
                $route = new ITH_Route($superArray);
                if ($result = $route->ith_route_run()) {
                    echo self::ith_response($result);
                    exit(self::SUCCESS_CODE);
                }
            }

            return null;
        }

        /**
         * @param array $message
         * @return mixed|string|void
         */
        public static function ith_response(array $message = [])
        {
            if (!headers_sent()) {
                header('HTTP/1.0 200 OK');
                header('Content-Type: application/json');
            }

            return json_encode($message);
        }

        /**
         * @param $key
         * @return string
         */
        public static function hash($key)
        {
            return md5(preg_replace('#[^a-z0-9]|\s+#', '', strtolower($key)));
        }
    }
}