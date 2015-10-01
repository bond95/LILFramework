<?php
class DBController 
{
	static private $dbdriver_class;
	static private $builder;
	static private $Functions = array("CreateConnection", "SelectQuery", "CreateTable", "InsertQuery", "DropTable");
	static public function SetDBDriver($driver)
	{
		PathDriver::Using(array(PathDriver::DATABASE => array($driver)));
		self::$dbdriver_class = $driver;
	}
	static public function __callStatic($method, $parameters) {
		if (in_array($method, self::$Functions)) {
			$driver = self::$dbdriver_class;
			return call_user_func_array(array($driver, $method), $parameters);
		}
		else
			return array(false, "Function doesn't exist");
	}
	static public function GetBuilder() {
		return self::$builder;
	}
	static public function SetBuilder($builder) {
		PathDriver::Using(array(PathDriver::DATABASE => array($builder)));
		self::$builder = $builder;
	}
}
DBController::SetDBDriver($GLOBALS['configuration']["DBController"]["ControllerClass"]);
DBController::SetBuilder($GLOBALS['configuration']["DBController"]["StatementBuilder"]);
DBController::CreateConnection($GLOBALS['configuration']["DBController"]["ControllerConfig"]);
?>