<?php

class MysqlParamsDriver {
	const SELECT_STATMENT = "SELECT %ret_colums% FROM `%table%` %where_stat%";
	const CREATE_TABLE = "CREATE TABLE `%table%` (%fields%);";
	const INSERT_STATEMENT = "INSERT INTO `%table%`(%colums%) VALUES (%values%);";
	const DROP_TABLE = "DROP TABLE `%table%`";

	public static function PrepareStatement($stat, $type) {
		if ($type == self::SELECT_STATMENT)
			return self::PrepareSelectStatement($stat);
		elseif ($type == self::CREATE_TABLE)
			return self::PrepareCreateTableStatement($stat);
		elseif ($type == self::INSERT_STATEMENT)
			return self::PrepareInsertStatement($stat);
		elseif ($type == self::DROP_TABLE)
			return self::PrepareDropTableStatement($stat);
	}

	protected static function PrepareSelectStatement($stat) {
		$table = $stat->Table;
		$colums = "";
		$where_stat = "";
		if (property_exists($stat, "Colums"))
		{
			foreach ($stat->Colums as $value) {
				if (!empty($colums))
					$colums .= ", ";
				$colums .= "`".$value."`";
			}
		}
		else
			$colums = "*";

		if (property_exists($stat, "Where"))
		{
			$where_stat = "WHERE ";
			foreach ($stat->Where as $value) {
				$where_stat .= $value->first.$value->operation.$value->second." ".$value->after;
			}
		}
		$result = str_replace("%ret_colums%", $colums, self::SELECT_STATMENT);
		$result = str_replace("%table%", $table, $result);
		$result = str_replace("%where_stat%", $where_stat, $result);
		return $result;
	}

	protected static function PrepareCreateTableStatement($params) 
	{
		$table = $params->Table;
		$fields = "";
		if (property_exists($params, "Fields"))
		{
			foreach ($params->Fields as $key => $value) {
				if ($fields != "")
					$fields .= ", ";
				$fields .= "`".$key."` ".$value->Type;
				if ($value->AutoIncrement)
				{
					$fields .= " NOT NULL AUTO_INCREMENT PRIMARY KEY";
				}
			}
		}
		$result = str_replace("%fields%", $fields, self::CREATE_TABLE);
		$result = str_replace("%table%", $table, $result);
		return $result;
	}

	protected static function PrepareInsertStatement($params) {
		$table = $params->Table;
		$values = "";
		$fields = "";
		if (property_exists($params, "Values"))
		{
			foreach ($params->Values as $key => $value) {
				if ($fields != "")
					$fields .= ", ";
				if ($values != "")
					$values .= ", ";
				$values .= "\"".$value."\"";
				$fields .= "`".$key."`";
			}
		}
		$result = str_replace("%colums%", $fields, self::INSERT_STATEMENT);
		$result = str_replace("%table%", $table, $result);
		$result = str_replace("%values%", $values, $result);
		return $result;
	}
	protected static function PrepareDropTableStatement($params) {
		$table = $params->Table;
		$result = str_replace("%table%", $table, self::DROP_TABLE);
		return $result;
	}
}
?>