<?php

PathDriver::Using(array(PathDriver::DATABASE => array("MysqlParamsDriver")));

class DBDriverMysql {
	private static $mysql_client;
	public static function CreateConnection($config_file)
	{
		$servername = "";
		$username = "";
		$password = "";
		$dbname = "";
		if(file_exists(PathDriver::$ROOT_DIR.PathDriver::CONFIG.$config_file))
		{
			$xml = simplexml_load_file(PathDriver::$ROOT_DIR.PathDriver::CONFIG.$config_file);
			$servername = $xml->Configuration[0]["servername"];
			$username = $xml->Configuration[0]["username"];
			$password = $xml->Configuration[0]["password"];
			$dbname = $xml->Configuration[0]["dbname"];
		}
		else
			die("Configuration file doesn't exist.");
		if (empty(self::$mysql_client)) {
			self::$mysql_client = new mysqli($servername, $username, $password, $dbname);

			// Check connection
			if (self::$mysql_client->connect_error) {
			    die("Connection failed: " . self::$mysql_client->connect_error);
			}
		}
	}
	public static function SelectQuery($params)
	{
		$result_array = array();
		$result = self::$mysql_client->query(
			MysqlParamsDriver::PrepareStatement($params, MysqlParamsDriver::SELECT_STATMENT));
		while ($obj = $result->fetch_assoc()) {
        	$result_array[] = $obj;
    	}
    	return self::ReturnResult($result_array);
	}
	public static function CreateTable($fields)
	{
		$result = self::$mysql_client->query(
			MysqlParamsDriver::PrepareStatement($fields, MysqlParamsDriver::CREATE_TABLE));
		return self::ReturnResult($result);
	}
	public static function InsertQuery($params)
	{
		$result = self::$mysql_client->query(
			MysqlParamsDriver::PrepareStatement($params, MysqlParamsDriver::INSERT_STATEMENT));
		return self::ReturnResult($result);
	}
	public static function DropTable($params)
	{
		$result = self::$mysql_client->query(
			MysqlParamsDriver::PrepareStatement($params, MysqlParamsDriver::DROP_TABLE));
		return self::ReturnResult($result);
	}
	private static function ReturnResult($res)
	{
		if (self::$mysql_client->error) {
		    return array(false, self::$mysql_client->error);
		}
		else
			return array(true, $res);
	}
}
?>
