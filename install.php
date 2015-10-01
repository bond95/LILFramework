<?php
include_once("Drivers/PathDriver.php");
$configuration = include_once("config.php");

PathDriver::Using(array(PathDriver::TABLES => array("TableController")));

$res = TableController::InstallTables();
if (!$res[0])
	echo $res[1];
?>
