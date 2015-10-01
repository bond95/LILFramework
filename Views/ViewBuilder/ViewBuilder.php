<?php

class ViewBuilder {
	const IF_STATEMENT = "if";
	const ENDIF_STATEMENT = "endif";
	const FOR_STATEMENT = "for";
	const ENDFOR_STATEMENT = "endfor";
	const ELSE_STATEMENT = "else";
	const IF_EQUAL = "==";
	const T_TOKENS = 0;
	const T_FULL_COMMAND = 1;
	const T_CALLBACK = 2;
	const T_END_COMMAND = 3;
	const T_ELSE_COMMAND = 4;
	private $ViewClass;
	public function __construct($view_class)
	{
		$this->ViewClass = $view_class;
	}
	public function Prepare($temp_name, $params)
	{
		$templates = file_get_contents(PathDriver::$ROOT_DIR.PathDriver::VIEWS.$this->ViewClass."/View/".$temp_name);
		$command = array();
		// while(count($templates))
		// {
		return self::DoCommands($templates, $params);
		// }
		//echo self::SetAllVariables($templates, $params);
	}
	public function PrepareWithMerge($main, $merges, $params)
	{
		$main_template = file_get_contents(PathDriver::$ROOT_DIR.PathDriver::VIEWS.$this->ViewClass."/View/".$main);
		foreach ($merges as $key => $value)
		{
			$merges_template = file_get_contents(PathDriver::$ROOT_DIR.PathDriver::VIEWS.$this->ViewClass."/View/".$value);
			$main_template = preg_replace("/\{\{ *".$key." *\}\}/", $merges_template, $main_template);
		}
		//$main_template = preg_replace("/\{\{ *(\d+) *\}\}/", "", $main_template);
		return self::DoCommands($main_template, $params);
	}
	protected static function SetAllVariables($text, $params)
	{
		$command = array();
		if(preg_match_all("/\{\{([A-Za-z0-9 .]+)\}\}/", $text, $command, PREG_OFFSET_CAPTURE))
		{
			foreach ($command[1] as $key => $value) {
				$text = str_replace($command[0][$key][0], self::GetVariableValue($value[0], $params), $text);
			}
		}
		return $text;
	}
	protected static function GetVariableValue($text, $params)
	{
		$variables = explode('.', str_replace(" ", "", $text));
		$val = $params[$variables[0]];
		for ($i = 1; $i < count($variables); $i++)
		{
			if (isset($val[$variables[$i]]))
				$val = $val[$variables[$i]];
			else
			{
				$val = "";
				break;
			}
		}
		return $val;
	}
	protected static function DoCommands($text, $params)
	{
		$result = "";
		$parsed_tokens = array();
		if(preg_match_all("/\{\%([A-Za-z0-9 .\"\'=><\]\[]+)\%\}/", $text, $command, PREG_OFFSET_CAPTURE))
		{
			$if_statements = 0;
			$if_positions = array();
			$for_statements = 0;
			$for_position = array();
			foreach ($command[1] as $key => $value) {
				preg_match_all("/[A-Za-z0-9.\"\'=><]+/", $value[0], $tokens);
				$tokens[self::T_FULL_COMMAND] = $command[0][$key];
				switch($tokens[0][0])
				{
					case self::IF_STATEMENT:
						$if_positions[$if_statements] = count($parsed_tokens);
						$if_statements++;
						$tokens[self::T_CALLBACK] = "IfStat";
						break;
					case self::ENDIF_STATEMENT:
						if ($if_statements)
						{
							$parsed_tokens[$if_positions[$if_statements-1]][self::T_END_COMMAND] = count($parsed_tokens);
							$tokens[self::T_CALLBACK] = "GetAmongText";
							$if_statements--;
							unset($if_positions[$if_statements]);
						}
						break;
					case self::ELSE_STATEMENT:
						if ($if_positions[$if_statements-1] == (count($parsed_tokens)-1))
						{
							$parsed_tokens[$if_positions[$if_statements-1]][self::T_ELSE_COMMAND] = count($parsed_tokens);
							$tokens[self::T_CALLBACK] = "GetAmongText";
						}
						break;
					case self::FOR_STATEMENT:
						$for_position[$for_statements] = count($parsed_tokens);
						$for_statements++;
						$tokens[self::T_CALLBACK] = "ForStat";
						break;
					case self::ENDFOR_STATEMENT:
						if ($for_statements)
						{
							$parsed_tokens[$for_position[$for_statements-1]][self::T_END_COMMAND] = count($parsed_tokens);
							$tokens[self::T_CALLBACK] = "GetAmongText";
							$for_statements--;
							unset($for_position[$for_statements]);
						}
						break;
				}
				$parsed_tokens[] = $tokens;		
			}
		}
		$result = self::GetAmongText($text, $params, $parsed_tokens, -1);
		for ($i = 0; $i < count($parsed_tokens); $i++) {
			if (isset($parsed_tokens[$i][2]))
			{
				$func = $parsed_tokens[$i][2];
				$result .= self::$func($text, $params, $parsed_tokens, $i);
			}
		}
		return $result;
	}
	private static function IfStat($text, $params, $all_commands, &$id)
	{
		$result = false;
		$tokens = $all_commands[$id];
		if (count($tokens[self::T_TOKENS]) == 2)
		{
			if (self::GetVariableValue($tokens[self::T_TOKENS][1], $params))
			{
				$result = true;
			}
		}
		elseif (count($tokens[self::T_TOKENS]) == 4) {
			$first_value = self::GetVariableValue($tokens[self::T_TOKENS][1], $params);
			$second_value = str_replace("\"", "", $tokens[self::T_TOKENS][3]);

			switch ($tokens[self::T_TOKENS][2])
			{
				case self::IF_EQUAL:
					if ($first_value == $second_value)
					{
						$result = true;
					}
					break;
			}
		}
		if ($result)
		{

			$parsed_text = self::GetAmongText($text, $params, $all_commands, $id);
			$id = $tokens[self::T_END_COMMAND]-1;
			return $parsed_text;
		}
		else
		{
			if (isset($tokens[self::T_ELSE_COMMAND]))
				$id = $tokens[self::T_ELSE_COMMAND]-1;
			else
				$id = $tokens[self::T_END_COMMAND]-1;
			return "";
		}
	}
	private static function ForStat($text, $params, $all_commands, &$id)
	{
		$tokens = $all_commands[$id];
		$array_value_name = $tokens[self::T_TOKENS][1];
		$array_value = self::GetVariableValue($tokens[self::T_TOKENS][3], $params);
		$result = "";
		foreach ($array_value as $key => $value) {
			$params[$array_value_name] = $value;
			$result .= self::GetAmongText($text, $params, $all_commands, $id);
			for ($i = $id+1; $i < $tokens[self::T_END_COMMAND]; $i++) {
				if (isset($all_commands[$i][2]))
				{
					$func = $all_commands[$i][2];
					$result .= self::$func($text, $params, $all_commands, $i);
				}
			}
		}
		unset($params[$array_value_name]);
		$id = $tokens[self::T_END_COMMAND] - 1;
		return $result;
	}
	private static function GetAmongText($text, $params, $all_commands, $id)
	{
		if (($id+1) < count($all_commands))
			$position_end = $all_commands[$id+1][self::T_FULL_COMMAND][1];
		else
			$position_end = strlen($text);
		if ($id > -1){
			$tokens = $all_commands[$id];
			$position_start = $tokens[self::T_FULL_COMMAND][1] + strlen($tokens[self::T_FULL_COMMAND][0]);
		}
		else
			$position_start = 0;

		$text_part = substr($text, $position_start, $position_end - $position_start);
		$text_part = self::SetAllVariables($text_part, $params);
		return $text_part;	
	}
}
?>