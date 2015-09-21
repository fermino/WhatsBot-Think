<?php
	require_once 'class/Lib/_Loader.php';

	require_once 'class/WhatsBot.php';

	require_once 'class/WhatsApp.php';

	require_once 'class/Parser.php';

	require_once 'class/ModuleManager.php';
	require_once 'class/ThreadManager.php';

	require_once 'class/Listeners/Core.php';

	class WhatsApiEventsManager
	{
		private $Listeners = array();

		public function LoadListeners(WhatsBot $WhatsBot, WhatsApp $WhatsApp, WhatsBotParser $Parser, ModuleManager $ModuleManager, ThreadManager $ThreadManager, Array $ToLoad = array())
		{
			Std::Out();
			Std::Out('[Info] [Events] Loading');

			$Listeners = Config::Get('Listeners');

			if(!is_array($Listeners))
			{
				Std::Out("[Warning] [Events] config/Listeners.json must be an array");

				$Listeners = array();
			}

			$Listeners = array_merge($ToLoad, $Listeners);

			foreach($Listeners as $Listener)
			{
				if(!empty($Listener) && is_string($Listener))
				{
					if($Listener[0] != '-')
					{
						$ListenerClass = $Listener . 'Listener';

						$Path = "class/Listeners/{$Listener}.php";

						if(basename(dirname(realpath($Path))) === 'Listeners')
						{
							// Lint

							require_once $Path;

							if(class_exists($ListenerClass))
							{
								if(is_subclass_of($ListenerClass, 'WhatsBotListenerCore'))
								{
									$ListenerInstance = new $ListenerClass($WhatsBot, $WhatsApp, $Parser, $ModuleManager, $ThreadManager);

									$this->BindListener($ListenerInstance, $Listener);
								}
								else
									Std::Out("[Warning] [Events] Can't load {$ListenerClass}. Class must inherit from WhatsBotListenerCore");
							}
							else
								Std::Out("[Warning] [Events] Can't load {$ListenerClass}. Class doesn't exist");
						}
						else
							Std::Out("[Warning] [Events] Can't load {$ListenerClass}. {$Listener}.php is not in class/Listeners");
					}
					else
						$this->DisableListener(substr($Listener, 1, strlen($Listener)));
				}
				else
					Std::Out('[Warning] [Events] Empty/non-string listener name');
			}

			Std::Out('[Info] [Events] Ready!');
		}

		public function BindListener($Listener, $Key = null)
		{
			if(is_object($Listener))
			{
				$ClassName = get_class($Listener); // use class name instead keys?

				if($Key === null)
					$Key = $this->GetNewKey();

				if(is_int($Key) || is_string($Key))
				{
					if(isset($this->Listeners[$Key]))
						Std::Out("[Warnng] [Events] Listener {$ClassName} : {$Key} will be overwrited");

					$this->Listeners[$Key][0] = $ClassName;
					$this->Listeners[$Key][1] = $Listener;
					$this->Listeners[$Key][2] = true;

					Std::Out("[Info] [Events] {$ClassName} : {$Key} binded");

					return $Key;
				}
				else
					Std::Out("[Warning] [Events] Trying to bind {$ClassName} with illegal offset type (" . var_export($Key, true) . ')');
			}
			else
				Std::Out('[Warning] [Events] Trying to bind non-object (' . var_export($Key, true) . ')');

			return false;
		}

		private function GetNewKey()
		{
			$Max = -1;

			foreach(array_keys($this->Listeners) as $Key)
				if(is_int($Key) && $Key > $Max)
					$Max = $Key;

			return $Max + 1;
		}

		public function UnbindListener($Key)
		{
			if(isset($this->Listeners[$Key]))
			{
				$ClassName = $this->Listeners[$Key][0];

				unset($this->Listeners[$Key]);

				Std::Out("[Info] [Events] {$ClassName} : {$Key} unbinded");

				return true;
			}

			Std::Out("[Warning] [Events] Trying to unbind non-existig listener ({$Key})");

			return false;
		}

		public function EnableListener($Key)
		{
			if(isset($this->Listeners[$Key]))
			{
				$this->Listeners[$Key][2] = true;

				Std::Out("[Info] [Events] {$this->Listeners[$Key][0]} : {$Key} enabled");
				
				return true;
			}

			Std::Out("[Warning] [Events] Trying to enable non-existig listener ({$Key})");

			return false;
		}

		public function DisableListener($Key)
		{
			if(isset($this->Listeners[$Key]))
			{
				$this->Listeners[$Key][2] = false;

				Std::Out("[Info] [Events] {$this->Listeners[$Key][0]} : {$Key} disabled");

				return true;
			}

			Std::Out("[Warning] [Events] Trying to disable non-existig listener ({$Key})");

			return false;
		}

		public function Fire($Event, Array $Params = array())
		{
			foreach($this->Listeners as $Listener)
				if($Listener[2])
					if(method_exists($Listener[1], $Event) && is_callable(array($Listener[1], $Event)))
						call_user_func_array(array($Listener[1], $Event), $Params);
		}
	}