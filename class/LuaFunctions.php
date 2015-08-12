<?php
	function IsLuaFilename(&$Filename, $TrimExtension = true)
	{
		if(substr(strtolower($Filename), strlen($Filename) - 4) === '.lua')
		{
			if($TrimExtension)
				$Filename = substr($Filename, 0, strlen($Filename) - 4);

			return true;
		}

		return false;
	}

	function LuaFixArray(Array $Array)
	{
		if(array_key_exists(0, $Array))
		{
			$Keys = array_keys($Array);
			
			for($i = count($Keys) - 1; $i >= 0; $i--)
				if(is_int($Keys[$i]) || strval(intval($Keys[$i])) === $Keys[$i])
					$Keys[$i] = intval($Keys[$i]) + 1;

			return array_combine($Keys, array_values($Array));
		}

		return $Array;
	}

	function LuaFixArrayRecursive(Array $Array)
	{
		$Array = LuaFixArray($Array);

		foreach(array_keys($Array) as $Key)
			if(is_array($Array[$Key]))
				$Array[$Key] = LuaFixArrayRecursive($Array[$Key]);

		return $Array;
	}