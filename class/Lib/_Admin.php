<?php
	require_once '_Loader.php';

	class Admin
	{
		public static function GetList()
		{
			return Config::Get('Admins');
		}

		public static function Is($Number)
		{
			$Number = self::GetNumber($Number);

			$Admins = Config::Get('Admins');

			if($Admins !== false)
				return in_array($Number, self::GetNumbersFromList($Admins));

			return false;
		}

		public static function Add($Number, $Nickname = null)
		{
			$Admins = Config::Get('Admins');

			if($Admins !== false)
			{
				if(!in_array($Number, self::GetNumbersFromList($Admins)))
				{
					$Admins[] = array($Number, $Nickname);

					$Saved = Config::Set('Admins', $Admins);

					if($Saved)
						Std::Out("[Info] [Admin] {$Number}" . (empty($Nickname) ? null : ":{$Nickname}") . ' added');
					else
						Std::Out("[Warning] [Admin] Error while trying to add {$Number}" . (empty($Nickname) ? null : ":{$Nickname}"));

					return $Saved;
				}
				else
				{
					Std::Out("[Info] [Admin] {$Number}" . (empty($Nickname) ? null : ":{$Nickname}") . ' already exists in admin list');

					return 1;
				}
			}
			else
			{
				$Saved = Config::Set('Admins', array(array($Number, $Nickname)));

				if($Saved)
					Std::Out("[Info] [Admin] {$Number}" . (empty($Nickname) ? null : ":{$Nickname}") . ' added');
				else
					Std::Out("[Warning] [Admin] Error while trying to add {$Number}" . (empty($Nickname) ? null : ":{$Nickname}"));

				return $Saved;
			}
		}

		public static function Delete($Index)
		{
			$Admins = Config::Get('Admins');

			if($Admins !== false)
			{
				$Index = intval($Index);

				if(isset($Admins[$Index]))
				{
					$Admin = $Admins[$Index];

					unset($Admins[$Index]);

					$Saved = Config::Set('Admins', array_values($Admins));

					if($Saved)
					{
						Std::Out("[Info] [Admin] {$Admin[0]}" . (empty($Admin[1]) ? null : ":{$Admin[1]}") . ' deleted');

						return array($Index, $Admin[0], $Admin[1]);
					}
					else
					{
						Std::Out("[Warning] [Admin] Error while trying to delete {$Admin[0]}" . (empty($Admin[1]) ? null : ":{$Admin[1]}"));
						
						return false;
					}
				}

				return 1;
			}

			return false;
		}

		private static function GetNumber($Number)
		{
			$Pos = strpos($Number, '@');

			if($Pos !== false)
				return substr($Number, 0, $Pos);

			return $Number;
		}

		private static function GetNumbersFromList($Admins)
		{
			$Function = function($Admin)
			{ return $Admin[0]; };

			return array_map($Function, $Admins);
		}
	}

	// Why array_map instead array_column?
	// Array_colum is only available in PHP >= 5.5.0