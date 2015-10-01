<?php
include_once("Drivers/PathDriver.php");
$configuration = include_once("config.php");
PathDriver::Using(array(PathDriver::DRIVERS => array("RouteDriver"),
	PathDriver::VIEWS => array("ViewController")));

echo RouteDriver::CallRoute($_GET['path']);
?>