<?php
	require_once 'Lib/_Loader.php';

	require_once 'Module.php';

	class LuaModule extends Module
	{
		protected $PathExtension = 'lua';
		protected $Extension = 'lua';

		private $Lua = null;

		protected function _Load()
		{
			try
			{
				require_once 'LuaWithPHP.php';
				require_once 'LuaFunctions.php';

				$this->Lua = new LuaWithPHP;

				$this->Lua->AssignUserConstants();

				$this->Lua->LinkObject($this, false, false, false);
				$this->Lua->AssignVariables(array('Key' => $this->Key, 'Name' => $this->Name, 'AliasOf' => $this->AliasOf, 'Path' => $this->Path, 'JPath' => $this->JPath, 'XPath' => $this->XPath, 'PathExtension' => $this->PathExtension, 'Extension' => $this->Extension, 'Data' => $this->Data, 'Loaded' => $this->Loaded, 'Enabled' => $this->Enabled));

				$this->Lua->LinkObjects(array($this->ModuleManager, $this->WhatsBot, $this->WhatsApp));

				return self::LOADED;
			}
			catch(Exception $Exception)
			{
				Std::Out("[Warning] [Modules] (Lua) Can't load {$this->Key}::{$this->Name} ({$this->AliasOf}). " . get_class($Exception) . 'thrown (' . $Exception->GetMessage() . ')');

				return self::NOT_LOADED;
			}
		}

		public function Execute(Message $Message, Array $Params = array())
		{
			try
			{
				if($Message->Time >= $this->WhatsBot->GetStartTime())
				{
					if($this->GetEnabled() === self::ENABLED)
					{
						if(is_readable($this->XPath))
						{
							$LangSection = "{$this->Key}_{$this->AliasOf}";

							$this->WhatsApp->SetLangSection($LangSection);
							$this->Lua->LinkObject(new Lang($LangSection), true, true, true);

							$this->Lua->AssignVariables($Params);

							$this->Lua->LinkObject($Message, true, false, false);

							$Return = $this->Lua->Include($this->XPath);

							if($Return === false)
								return INTERNAL_ERROR;
							
							if($Return === null)
								return self::EXECUTED;

							return $Return;
						}

						Std::Out("[Warning] [Modules] (Lua) Can't execute {$this->Key}::{$this->Name} ({$this->AliasOf}). {$this->PathExtension} file is not readable");

						return self::NOT_READABLE;
					}

					return self::NOT_ENABLED;
				}

				return self::EXECUTED;
			}
			catch(Exception $Exception)
			{
				Std::Out("[Warning] [Modules] (Lua) Can't execute {$this->Key}::{$this->Name} ({$this->AliasOf}). " . get_class($Exception) . 'thrown (' . $Exception->GetMessage() . ')');

				return INTERNAL_ERROR;
			}
		}
	}