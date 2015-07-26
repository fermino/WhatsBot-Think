<?php
	require_once 'class/Lib/_Loader.php';

	class WhatsApiEventsManager
	{
		private $Listeners = array();

		public function BindListener($Listener, $Key = null)
		{
			if(is_object($Listener))
			{
				$ClassName = get_class($Listener);

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