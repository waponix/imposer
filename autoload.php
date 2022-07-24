<?php

function apiAutoloader($class)
{
    include_once str_replace('\\', '/', $class) . '.php';
}

spl_autoload_register('apiAutoloader');