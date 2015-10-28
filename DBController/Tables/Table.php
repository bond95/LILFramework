<?php
PathDriver::Using(array(PathDriver::DATABASE => array("DBController")));
PathDriver::Using(array(PathDriver::DRIVERS => array("FormBuilder")));


class Table {
	protected $table_name;

	function __construct($class_name)
	{
		$this->table_name = str_replace("Table", "", $class_name);
	}

	public function Get()
	{
		$builder = DBController::GetBuilder();
		$stat = new $builder($builder::T_SELECT);
		$stat->SetTableName($this->table_name);
		return DBController::SelectQuery($stat->GetParams());
	}

	public function GetFilteredList($cond)
	{
		$cond->SetTableName($this->table_name);
		return DBController::SelectQuery($cond->GetParams());
	}

	public function GetById($id)
	{
		$builder = DBController::GetBuilder();
		$stat = new $builder($builder::T_SELECT_BY_ID);
		$stat->SetTableName($this->table_name)->PutId($id);		
		$result = DBController::SelectQuery($stat->GetParams());
		if(count($result)>0)
			return DBController::SelectQuery($stat->GetParams());
		else
			return new stdClass;
	}

	public function Set($save_data) 
	{
		$builder = DBController::GetBuilder();
		$stat = new $builder($builder::T_INSERT);
		$stat->SetTableName($this->table_name)->SetValuesFromObject($save_data);
		$result = DBController::InsertQuery($stat->GetParams());
		return $result;
	}

	public function Install()
	{

		$info = json_decode(file_get_contents(PathDriver::TABLES_MODEL.$this->table_name.".json"));
		$builder = DBController::GetBuilder();
		$stat = new $builder($builder::T_CREATE_TABLE);
		$stat->SetTableName($this->table_name)->PutFieldFromObject($info);
		$result = DBController::CreateTable($stat->GetParams());
		return $result;	
	}

	public function Drop()
	{
		$builder = DBController::GetBuilder();
		$stat = new $builder($builder::T_DROP_TABLE);
		$stat->SetTableName($this->table_name);
		$result = DBController::DropTable($stat->GetParams());
		return $result;
	}
		public function FormFields(){
		$form_fields = array();
		$info = json_decode(file_get_contents(PathDriver::TABLES_MODEL.$this->table_name.".json"), true);
		foreach ($info as $field_name => $attrs) {
			if (end($attrs)){
				reset($attrs);
				if (in_array("password", $attrs, true))
					$form_fields[$field_name] = "password";
				else	
					$form_fields[$field_name] = $attrs[0];
			}
		}
		return $form_fields;
	}
	public function MapResultToObject($result)
	{
		$fields = $this->FormFields();
		$obj = new stdClass();
		foreach ($fields as $key => $value) {
			if (isset($result[$key]))
			{
				$obj->$key = $result[$key];
			}
			else
			{
				return false;
			}

		}
		return $obj;
	}
	public function PrepareForm()
	{
		$form_builder = new FormBuilder();
		$fields = $this->FormFields();
		foreach ($fields as $key => $value)
		{
			$form_builder->PutInput($key, $this->GetFormType($value), "", $key);
		}
		$form_builder->SetFormName($this->table_name);
		return $form_builder;
	}
	private function GetFormType($type)
	{
		if ($type == "int")
			return "number";
		elseif ($type == "password") {
			return "password";
		}
		else
			return "text";
	}
}
?>