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