<?php

class BuilderController
{
	static private $view_builder_name;
	private $view_builder_instance;
	private $Functions = array("Prepare", "PrepareWithMerge");
	public function __construct($class_name)
	{
		if (!empty(self::$view_builder_name))
		{
			$builder = self::$view_builder_name;
			$this->view_builder_instance = new $builder($class_name);
		}
	}
	static public function SetViewBuilder($builder)
	{
		self::$view_builder_name = $builder;
		PathDriver::Using(array(PathDriver::VIEW_BUILDER => array($builder)));
	} 
	public function __call($method, $parameters) {
		if (in_array($method, $this->Functions)) {
			$builder = $this->view_builder_instance;
			return call_user_func_array(array($builder,$method), $parameters);
		}
		else
			return array(false, "Function doesn't exist");
	}
}

BuilderController::SetViewBuilder($GLOBALS['configuration']["Views"]["ViewBuilder"]);
?>