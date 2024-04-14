<?php 

namespace Test;

spl_autoload_register(function ($class) {
    if(substr($class, 0, strlen(__NAMESPACE__)) != __NAMESPACE__ && substr($class, 0, strlen('\\'.__NAMESPACE__)) != '\\'.__NAMESPACE__)
        return false;
    $str_path = realpath(__DIR__.DIRECTORY_SEPARATOR.str_replace('\\', DIRECTORY_SEPARATOR, $class).'.php');
    if(file_exists($str_path)) require_once $str_path;
});