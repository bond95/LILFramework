<?php

class ViewController
{
	private static $Views;
	public static function ScanViews()
	{
		self::$Views = array();
		foreach (glob(PathDriver::$ROOT_DIR.PathDriver::VIEWS."*[A-Za-z]View") as $value) {
			$name = str_replace(PathDriver::$ROOT_DIR.PathDriver::VIEWS, "", $value);
			self::$Views[] = $name;
		}
	}
	public static function FindView($name)
	{
		if (in_array($name."View", self::$Views))
		{
			$view_name = $name."View";
			return $view_name;
		}
		return false;
	}
	public static function FindMethodMyURL($view, $path)
	{
		$urls = include(PathDriver::VIEWS.$view."/url.config.php");
		foreach ($urls as $key => $value)
		{
			$match = array();
			if (preg_match($value, $path,$match))
			{
				return array($key, $match);
			}
		}
		return false;
	}
	public static function __callStatic($method, $parameters)
	{
		if (in_array($method, self::$Views))
		{
			PathDriver::Using(array(PathDriver::VIEWS.$method."/" => array($method)));
			return new $method();
		}
		return false;
	}
}
ViewController::ScanViews();
?>