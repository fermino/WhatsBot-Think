<?php
	require_once 'ThreadTasks/WhatsBot.php';
	require_once 'ThreadTasks/WhatsApp.php';
	require_once 'ThreadTasks/EventManager.php';
	require_once 'ThreadTasks/ModuleManager.php';
	require_once 'ThreadTasks/ThreadManager.php';

	trait ThreadTaskManager
	{
		private $Tasks = 'a:0:{}'; // serialized empty array, beacuse pthreads nullifies non-simple data types
		private $Return = 'a:0:{}';

		private $WhatsBot = null;
		private $WhatsApp = null;
		private $EventManager = null;
		private $ModuleManager = null;
		private $ThreadManager = null;

		private function LoadTaskManager()
		{ // What if isn't called ?
			$this->WhatsBot = new WhatsBotTasks($this);
			$this->WhatsApp = new WhatsAppTasks($this);
			$this->EventManager = new EventManagerTasks($this);
			$this->ModuleManager = new ModuleManagerTasks($this);
			$this->ThreadManager = new ThreadManagerTasks($this);
		}

		public function GetTasks()
		{
			$this->Lock();

			$Tasks = unserialize($this->Tasks);
			$this->Tasks = 'a:0:{}';

			$this->Unlock();

			return $Tasks;
		}

		public function AddTask($Type, $Method, Array $Params = array())
		{
			$this->Lock();

			$Tasks = unserialize($this->Tasks);
			$Tasks[] = array($Type, $Method, $Params);
			$this->Tasks = serialize($Tasks);

			$this->Unlock();
		}

		public function SetReturn($Type, $Name, $Value)
		{
			$this->Lock();

			$Return = unserialize($this->Return);
			$Return[$Type][$Name] = $Value;
			$this->Return = serialize($Return);

			$this->Unlock();
		}

		public function WaitFor($Type, $Name, $UnsetBefore = true, $UnsetAfter = true)
		{
			if($UnsetBefore)
			{
				$this->Lock();

				$Return = unserialize($this->Return);
				unset($Return[$Type][$Name]);
				$this->Return = serialize($Return);

				$this->Unlock();
			}

			while(true)
			{
				$this->Lock();

				$Return = unserialize($this->Return);

				if(isset($Return[$Type][$Name]))
				{
					$Value = $Return[$Type][$Name];

					if($UnsetAfter)
					{
						unset($Return[$Type][$Name]);
						$this->Return = serialize($Return);
					}

					$this->Unlock();

					return $Value;
				}

				$this->Unlock();

				sleep(1);
			}
		}
	}