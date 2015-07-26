<?php
	require_once 'Lib/_Loader.php';

	require_once 'Module.php';

	trait ModuleManagerExists
	{
		public function KeyExists($Key)
		{
			$Exists = in_array($Key, $this->GetKeys());

			if(!$Exists)
				Std::Out("[Warning] [Modules] Key {$Key} doesn't exists");

			return $Exists;
		}

		public function ModuleExists($Key, $Name, $ShowWarn = true)
		{
			$Name = strtolower($Name);

			if(!empty($this->Modules[$Key][$Name]))
				return Module::LOADED;

			if($ShowWarn)
				Std::Out("[Warning] [Modules] Module {$Key}::{$Name} doesn't exists");

			return Module::NOT_LOADED;
		}

		abstract public function GetKeys();
	}