<?php

defined('ITH_DEFINED') or die('No direct script access.');

class BaseAction implements BaseActionInterface
{
    public function run($entity)
    {
    }

    /**
     * @return string
     */
    public function verbs()
    {
        return 'POST';
    }

    /**
     * @return bool
     */
    public function access()
    {
        return true;
    }

    /**
     * @param null $result
     * @return null
     */
    public function prepareResult($result = null)
    {
        return $result;
    }
}