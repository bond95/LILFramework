<?php
class PathDriver {
	const DATABASE = "DBController/";
	const DRIVERS = "Drivers/";
	const CONFIG = "config/";
	const TABLES = "DBController/Tables/";
	const PREV_DIR = "../";
	const PHP_EXT = ".php";
	const TABLES_MODEL = "DBController/Tables/TablesJSON/";
	const VIEWS = "Views/";
	const VIEW_BUILDER = "Views/ViewBuilder/";
	const UTILITIES = "utilities/";
	public static $ROOT_DIR;
	static function Using($using_classes)
	{
		foreach($using_classes as $dir => $classes)
		{
			foreach ($classes as $cls) {
				if (file_exists(self::$ROOT_DIR.$dir.$cls.self::PHP_EXT))
				{
					include_once(self::$ROOT_DIR.$dir.$cls.self::PHP_EXT);
				}
			}
		}
	}
}

PathDriver::$ROOT_DIR = __DIR__."/".PathDriver::PREV_DIR;
?>