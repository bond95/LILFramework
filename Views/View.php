<?php

PathDriver::Using(array(PathDriver::VIEWS => array("BuilderController", "")));
/**
 * Created by PhpStorm.
 * User: bohdan
 * Date: 03.09.15
 * Time: 12:10
 */
class View
{
    private $builder;
    public function __construct($class_name)
    {
        $this->builder = new BuilderController($class_name);
    }
    public function GetBuilder()
    {
        return $this->builder;
    }
    public function GetSafeString($str)
    {
        $result = stripcslashes($str);
        $result = strip_tags($result);
        $result = htmlspecialchars($result, ENT_QUOTES);
        return $result;
    }
}