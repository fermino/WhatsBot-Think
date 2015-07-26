<?php
	require_once 'Lib/_Loader.php';

	require_once 'Module.php';

	require_once 'PHPModule.php';

	require_once 'LuaModule.php';
	require_once 'LuaFunctions.php';

	trait ModuleManagerLoader
	{
		public function LoadModules()
		{
			Std::Out();
			Std::Out('[Info] [Modules] Loading');

			$Modules = Config::Get('Modules');

			if(is_array($Modules))
			{
				$Loaded = array();

				$Keys = array_keys($Modules);

				foreach($Keys as $Key)
				{
					foreach($Modules[$Key] as $Module)
					{
						if(is_string($Module))
							$Module = array($Module, $Module);

						if(is_array($Module) && !empty($Module[0]) && !empty($Module[1]))
						{
							$Name = strtolower($Module[0]);

							$Loaded[$Key][$Name] = $this->LoadModule($Key, $Name, $Module[1]);
						}
						else
						{
							Std::Out('[Warning] [Modules] Config must be Key::Name or Key::[Name, AliasOf]');
							Std::Out("{$Key}::", false);
							Std::Out(var_export($Module, true));
						}
					}
				}

				Std::Out('[Info] [Modules] Ready!'); // ($N loaded modules)

				return $Loaded;
			}

			Std::Out('[Warning] [Modules] Config file is not an array');

			return false;
		}

		public function LoadModule($Key, $Name, $AliasOf)
		{
			if($this->KeyExists($Key))
			{
				$Name = strtolower($Name);
				$AliasOf = strtolower($AliasOf);

				$Lua = false;

				if(IsLuaFilename($Name))
					$Lua = true;
				if(IsLuaFilename($AliasOf))
					$Lua = true;

				if($Lua)
					$this->Modules[$Key][$Name] = new LuaModule($this, $this->WhatsBot, $this->WhatsApp, $Key, $Name, $AliasOf);
				else
					$this->Modules[$Key][$Name] = new PHPModule($this, $this->WhatsBot, $this->WhatsApp, $Key, $Name, $AliasOf);

				$Loaded = $this->Modules[$Key][$Name]->IsLoaded();

				if(!$Loaded)
					$this->UnloadModule($Key, $Name);

				return $Loaded;
			}

			return false;
		}

		public function UnloadModule($Key, $Name)
		{
			$Name = strtolower($Name);

			if($this->ModuleExists($Key, $Name))
			{
				unset($this->Modules[$Key][$Name]);

				return true;
			}

			return false;
		}

		abstract public function KeyExists($Key);
		abstract public function ModuleExists($Key, $Name, $ShowWarn = true);
	}