<?php
	require_once '_Loader.php';

	require_once 'class/LuaFunctions.php';

	class Regex
	{
		const URL = '#\bhttps?://[^\s()<>]+(?:\([\w\d]+\)|([^[:punct:]\s]|/))#i';

		public static function MatchAll($Pattern, $Subject, $FixArray = false)
		{
			preg_match_all($Pattern, $Subject, $Matches);

			if($FixArray)
				return LuaFixArrayRecursive($Matches[0]);
			else
				return $Matches[0];
		}
	}