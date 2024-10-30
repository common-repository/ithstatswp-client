<?php

defined('ITH_DEFINED') or die('No direct script access.');

if (!class_exists('ITH_Route')) {
    class ITH_Route
    {
        protected $_action;
        protected $_auth_key;
        protected $_entity;

        public function __construct(array $data = [])
        {
            $this->_action = $data['ith_action'];
            $this->_auth_key = array_key_exists('ith_auth_key', $data) ? $data['ith_auth_key'] : null;
            $this->_entity = array_key_exists('ith_entity', $data) ? $data['ith_entity'] : [];
        }

        /**
         * @return bool
         */
        public function ith_route_exists()
        {
            if (!file_exists(ITH_CLIENT_PLUGIN_DIR . 'includes/actions/' . $this->_action . '.php'))
                return false;

            return true;
        }

        /**
         * @return mixed|null
         */
        public function ith_route_run()
        {
            if ($this->ith_route_exists()) {
                if (!ITH_Auth::authenticate($this->_auth_key) && call_user_func([new $this->_action, 'access']))
                    return call_user_func_array(['IthForbiddenAction', 'prepareResult'], [['status' => 0, 'msg' => 'Access denied!']]);

                if (strcasecmp($_SERVER['REQUEST_METHOD'], call_user_func([new $this->_action, 'verbs'])) <> 0)
                    return call_user_func_array([new $this->_action, 'prepareResult'], [['status' => 0, 'msg' => 'Method not supported!']]);

                return call_user_func_array([new $this->_action, 'run'], ['entity' => $this->_entity]);
            } else {
                return call_user_func_array(['IthNotFoundAction', 'prepareResult'], [['status' => 0, 'msg' => 'Action not found!']]);
            }
        }
    }
}