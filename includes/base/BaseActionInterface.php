<?php

defined('ITH_DEFINED') or die('No direct script access.');

interface BaseActionInterface
{
    /**
     * @return mixed
     */
    public function run($entity);

    /**
     * @return mixed
     */
    public function verbs();

    /**
     * @return mixed
     */
    public function access();
}