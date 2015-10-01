<?php

class RouteDriver
{
	static public function CallRoute($path)
	{
		$view_name = array();
		preg_match("/^([A-Za-z]*)\/?/", $path, $vn);
		$view_name = "";
		if (isset($vn[1]) && !empty($vn[1]))
			$view_name = $vn[1];
		else
			$view_name = "Main";
		$view = "";
		$view = ViewController::FindView($view_name);
		if (!$view)
			$view = "MainView";
		else
			$path = str_replace($view_name, "", $path);
		if (strlen($path) > 0) {
			if ($result = ViewController::FindMethodMyURL($view, $path)) {
				$method = $result[0];
				$params = $result[1];
				return ViewController::$view()->$method($params);
			}
		}
		else {
			return ViewController::$view()->Main();
		}

	}
}
?>