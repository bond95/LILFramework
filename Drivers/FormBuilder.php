<?php

class FormBuilder
{
	private $inputs;
	private $method;
	private $action;
	public function __construct($method="", $action="", $name="")
	{
		$this->inputs = array();
		$this->method = $method;
		$this->action = $action;
		$this->name = $name;
	}
	public function PutInput($name, $type, $value="", $label="")
	{
		$obj = new stdClass();
		if (empty($name) || empty($type))
			return false;
		$obj->name = $name;
		$obj->type = $type;
		if (!empty($label))
			$obj->label = $label;
		if (!empty($value))
			$obj->value = $value;
		$this->inputs[$name] = $obj;
		return true;
	}
	public function GetInputById($id)
	{
		return ($this->inputs[$id]);
	}
	public function SetMethod($method)
	{
		$this->method = $method;
	}
	public function SetAction($action)
	{
		$this->action = $action;
	}
	public function SetFormName($name)
	{
		$this->name = $name;
	}
	public function BuildForm()
	{
		$form_text = "<form action=\"".$this->action."\" method=\"".$this->method."\" name=\"".$this->name."\">";
		$inputs_text = "";
		foreach ($this->inputs as $key=>$value) {
			if (isset($value->label))
				$inputs_text .= "<label for=\"id_".$value->name."\">".$value->label."</label>";
			$inputs_text .= "<input type=\"".$value->type."\" name=\"".$value->name."\" id=\"id_".$value->name."\" ";
			if (isset($value->value))
				$inputs_text .= "value=\"".$value->value."\"";
			$inputs_text .= "/><br>";
		}
		$inputs_text .= "<input type=\"submit\" name=\"sub_".$this->name."\" />";
		$form_text .= $inputs_text."</form>";
		return $form_text;
	}
}
?>