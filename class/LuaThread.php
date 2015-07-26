<?php
	require_once 'Lib/_Loader.php';

	require_once 'Thread.php';

	class LuaThread extends WhatsBotThread
	{
		protected $PathExtension = 'lua';
		protected $Extension = 'lua';

		protected function _Load()
		{
			require_once 'LuaWithPHP.php';

			return self::LOADED;
		}

		protected function Execute()
		{
			try
			{
				$Lua = new LuaWithPHP;

				$Lua->AssignUserConstants();

				$Lua->LinkObject($this, false, false, false);
				$Lua->AssignVariables(array('Name' => $this->Name, 'Path' => $this->Path, 'JPath' => $this->JPath, 'XPath' => $this->XPath, 'PathExtension' => $this->PathExtension, 'Data' => $this->Data, 'Loaded' => $this->Loaded));
				
				$Lua->LinkObjects(array($this->ThreadManager, $this->ModuleManager, $this->EventManager, $this->WhatsApp, $this->WhatsBot));
				$Lua->LinkObject(new Lang("Thread_{$this->Name}"), true, true, true);

				while(!$this->Stop && is_readable($this->XPath))
					if($Lua->Include($this->XPath) === false)
						$this->Stop('Lua fatal error');
			}
			catch(Exception $Exception)
			{
				Std::Out("[Warning] [Threads] (Lua) Can't execute {$this->Name}. " . get_class($Exception) . 'thrown (' . $Exception->GetMessage() . ')');
			}
		}
	}