<?php
PathDriver::Using(array(PathDriver::DATABASE => array("DBController")));


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
}
?>