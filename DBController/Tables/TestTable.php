<?php

PathDriver::Using(array(PathDriver::TABLES => array("Table")));

class TestTable extends Table
{
	function __construct()
	{
		parent::__construct(__CLASS__);
	}
}
?>