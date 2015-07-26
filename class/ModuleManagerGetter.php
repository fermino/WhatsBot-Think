<?php
	require_once 'Lib/_Loader.php';

	require_once 'Module.php';

	trait ModuleManagerGetter
	{
		public function GetKeys()
		{
			return array_keys($this->Modules);
		}

		public function GetModules($Key, $Type = 0)
		{
			if($this->KeyExists($Key))
			{
				return array_keys(array_filter
				(
					$this->Modules[$Key], 
					function($Module) use($Type)
					{
						if($Type == 1) // User module
							return empty($Module->GetData()['Admin']);
						elseif($Type == 2) // Admin module
							return !empty($Module->GetData()['Admin']);
						else
							return true;
					}
				));
			}

			return false;
		}

		public function GetModule($Key, $Name, $ShowWarn = true)
		{
			$Name = strtolower($Name);

			if($this->ModuleExists($Key, $Name, $ShowWarn) === Module::LOADED)
			{
				if($this->Modules[$Key][$Name]->IsLoaded())
					return $this->Modules[$Key][$Name];
				else
					return Module::LOAD_ERROR;
			}

			if($ShowWarn)
				Std::Out("[Warning] [Modules] Trying to get not loaded module. {$Key}::{$Name}");

			return Module::NOT_LOADED;
		}

		abstract public function KeyExists($Key);
		abstract public function ModuleExists($Key, $Name, $ShowWarn = true);
	}