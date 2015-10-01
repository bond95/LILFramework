<?php

class TableController
{
	private static $Tables;
	public static function ScanTables()
	{
		self::$Tables = array();
		foreach (glob(PathDriver::$ROOT_DIR.PathDriver::TABLES."*[A-Za-z]Table.php") as $value) {
			$name = str_replace(PathDriver::$ROOT_DIR.PathDriver::TABLES, "",
				str_replace(".php", "", $value));
			self::$Tables[] = $name;
		}
	}
	public static function __callStatic($method, $parameters)
	{
		if (in_array($method, self::$Tables))
		{
			PathDriver::Using(array(PathDriver::TABLES => array($method)));
			return new $method;
		}
		return false;
	}
	public static function InstallTables()
	{
		PathDriver::Using(array(PathDriver::TABLES => self::$Tables));
		$installed = array();
		foreach (self::$Tables as $value) {
			$res = (new $value)->Install();
			if ($res[0])
			{
				$installed[] = $value;
			}
			else
			{
				foreach ($installed as $table) {
					(new $table)->Drop();
				}
				return array(false, "Error in '{$value}'.");
			}
		}
		return array(true);
	}
}
TableController::ScanTables();
?>