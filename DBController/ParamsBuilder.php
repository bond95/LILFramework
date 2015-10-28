<?php

class ParamsBuilder {
	private $params;
	private $type;
	private $func_array;
	const T_SELECT = 1;
	const T_CREATE_TABLE = 2;
	const T_INSERT = 3;
	const T_SELECT_BY_ID = 4;
	const T_DROP_TABLE = 5;
	const COND_EQUAL = "=";
	const COND_LESS = "<";
	const COND_GREATER = ">";
	const COND_OR = "OR";
	const COND_AND = "AND";
	const F_INT = "int";
	const F_CHAR = "varchar(255)";
	const F_TEXT = "text";
	function __construct($type)
	{
		$this->params = new stdClass();
		$this->params->Table = "";
		$this->type = $type;
		$this->func_array = array();
		$this->func_array[self::T_SELECT] = array("PutReturnedColums", "PutCondition");
		$this->func_array[self::T_CREATE_TABLE] = array("PutField", "PutFieldFromObject");
		$this->func_array[self::T_INSERT] = array("SetValue", "SetValuesFromObject");
		$this->func_array[self::T_SELECT_BY_ID] = array("PutReturnedColums", "PutId");
		$this->func_array[self::T_DROP_TABLE] = array();

	}

	public function __call($method, $parameters)
	{
		if (isset($this->func_array[$this->type]) && in_array($method, $this->func_array[$this->type]))
		{
			return call_user_func_array(array($this, $method), $parameters);
		}
	}

	protected function PutReturnedColums($colum)
	{
		if (!property_exists($this->params, "Colums"))
			$this->params->Colums = array();
		$this->params->Colums[] = $colum;
		return $this;
	}
	public function SetTableName($name)
	{
		$this->params->Table = $name;
		return $this;
	}
	protected function PutCondition($cond_type, $first, $second)
	{
		if (!property_exists($this->params, "Where"))
			$this->params->Where = array();
		$cond = new stdClass();
		$cond->after = "";
		$cond->first = $first;
		$cond->operation = $cond_type;
		$cond->second = $second;
		$this->params->Where[] = $cond;
		return $this;
	}
	protected function PutLogicCondition($cond) {
		$this->params->Where[count($this->params->Where)]->after = $cond;
	}
	protected function PutId($Id)
	{
		$this->PutCondition(self::COND_EQUAL, "Id", $Id, "");
		return $this;
	}
	public function GetParams() {
		return $this->params;
	}
	protected function PutField($name, $type, $ai = false) {
		if (!property_exists($this->params, "Fields"))
			$this->params->Fields = array();
		$field = new stdClass();
		$field->Name = $name;
		$field->Type = $type;
		$field->AutoIncrement = $ai;
		$this->params->Fields[$name] = $field;
		return $this;
	}
	protected function SetValue($name, $value)
	{
		if (!property_exists($this->params, "Values"))
			$this->params->Values = array();
		$this->params->Values[$name] = $value;
		return $this;
	}
	protected function SetValuesFromObject($obj) 
	{
		if (!property_exists($this->params, "Values"))
			$this->params->Values = array();
		foreach ($obj as $key => $value) {
			$this->params->Values[$key] = $value;
		}
		return $this;
	}
	protected function PutFieldFromObject($obj) 
	{
		if (!property_exists($this->params, "Fields"))
			$this->params->Fields = array();
		foreach ($obj as $key => $value) {
			$field = new stdClass();
			$field->Name = $key;
			$field->Type = $value[0];
			if (in_array("auto_increment", $value, true) && $value[0] == "int")
				$field->AutoIncrement = true;
			else
				$field->AutoIncrement = false;
			$this->params->Fields[$key] = $field;
		}
		return $this;
	}
}
?>