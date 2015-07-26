<?php
	require_once 'Lib/_Loader.php';

	require_once 'WhatsAPI/whatsprot.class.php';

	require_once 'WhatsApp.php';

	require_once 'Listener.php';

	require_once 'Parser.php';

	require_once 'ModuleManager.php';

	require_once 'ThreadManager.php';

	class WhatsBot
	{
		private $WhatsProt = null;
		private $WhatsApp = null;
		private $Password = null;

		private $Listener = null;
		private $Parser = null;

		private $ModuleManager = null;

		private $ThreadManager = null;

		private $Debug = false;
		private $StartTime = null;

		private $Exit = false;

		public function __construct($Debug = false)
		{
			$this->Debug = (bool)$Debug;

			Std::Out();
			Std::Out('[Info] [WhatsBot] Loading... Debug = ' . var_export($this->Debug, true));

			Config::Load();
			LoadLibs();

			$Config = Config::Get('WhatsBot');

			if(!empty($Config['WhatsApp']['Username']) && !empty($Config['WhatsApp']['Nickname']) && !empty($Config['WhatsApp']['Password']))
			{
				$this->Password = $Config['WhatsApp']['Password'];

				# WhatsApp

				Std::Out();
				Std::Out("[Info] [WhatsBot] I'm {$Config['WhatsApp']['Nickname']} ({$Config['WhatsApp']['Username']})");

				$this->WhatsProt = new WhatsProt($Config['WhatsApp']['Username'], $Config['WhatsApp']['Nickname'], $this->Debug);

				$this->WhatsApp = new WhatsApp($this->WhatsProt);

				# WhatsBot

				$this->ModuleManager = new ModuleManager($this, $this->WhatsApp);

				$this->ThreadManager = new ThreadManager($this, $this->WhatsProt, $this->ModuleManager);

				$this->Parser = new WhatsBotParser($this->WhatsApp, $this->ModuleManager);

				$this->Listener = new WhatsBotListener($this->WhatsApp, $this->Parser);

				# Load

				$this->ModuleManager->LoadModules();

				$this->ThreadManager->LoadThreads();

				# Binding

				$this->LoadListeners();
			}
			else
				throw new Exception('You have to setup the config file config/WhatsBot.json');
		}

		private function LoadListeners()
		{
			Std::Out();
			Std::Out('[Warning] [Events] Loading');

			$LoadWhatsBotListener = true;

			$Listeners = Config::Get('Listeners');

			if(is_array($Listeners))
			{
				foreach($Listeners as $Listener)
				{
					if($Listener != '-WhatsBotListener')
					{
						require_once "class/Listeners/{$Listener}.php";

						$ListenerInstance = new $Listener($this, $this->WhatsApp, $this->Parser, $this->ModuleManager, $this->ThreadManager);

						$this->WhatsApp->EventManager()->BindListener($ListenerInstance, $Listener);
					}
					else
						$LoadWhatsBotListener = false;
				}
			}
			else
				Std::Out("[Warning] [Events] config/Listeners.json must be an array");

			if($LoadWhatsBotListener)
				$this->WhatsApp->EventManager()->BindListener($this->Listener, 'WhatsBotListener');

			Std::Out('[Warning] [Events] Ready!');
		}

		private function Connect($Show = true, $Retries = 3) // Don't hardcode D:
		{
			if($Show)
				Std::Out();

			for($i = 1; $i <= $Retries; $i++)
			{
				try
				{
					$this->WhatsApp->Disconnect();

					if($Show === true)
						Std::Out('[Info] [WhatsBot] Connecting');

					$this->WhatsApp->Connect();

					if($Show === true)
						Std::Out('[Info] [WhatsBot] Logging in');

					$this->WhatsApp->LoginWithPassword($this->Password);

					if($Show)
					{
						Std::Out();
						Std::Out('[Info] [WhatsBot] Ready!');
					}

					return true;
				}
				catch(Exception $Exception)
				{
					$Class = get_class($Exception); // 3 catches?

					if($Class === 'ConnectionException')
						$Message = 'Connection error (' . $Exception->GetMessage() . ')';
					elseif($Class === 'LoginFailureException')
						$Message = 'Login failure';
					else
						$Message = "{$Class} thrown while connecting (" . $Exception->GetMessage() . ')';

					if($Show === true)
						Std::Out();

					$Show = 1;

					Std::Out("[Warning] [WhatsBot] {$Message}. Retry {$i}/{$Retries}...");

					// Log trace?
				}
			}

			if($Class === 'ConnectionException')
				$this->_Exit('Connection error');
			elseif($Class === 'LoginFailureException')
				$this->_Exit('Login failure');
			else
				$this->_Exit("{$Class} exception");

			return false;
		}

		public function Start($Show = true)
		{
			return $this->Connect($Show);
		}

		public function Listen($PingDelay = 60, $ReconnectDelay = 300)
		{
			# Check Exit

			if($this->Exit !== false)
			{
				Std::Out();
				Std::Out("[Info] [WhatsBot] Exiting ({$this->Exit})...");

				return $this->Exit;
			}

			$this->StartTime = time();

			$ReconnectTimer = $this->StartTime;
			$PingTimer = $this->StartTime;

			Std::Out();
			Std::Out("[Info] [WhatsBot] Start time is {$this->StartTime}");

			Std::Out();
			Std::Out('[Info] [WhatsBot] Listening...');

			# Start threads

			$this->ThreadManager->StartThreads();

			while(true)
			{
				try
				{
					# Check Exit

					if($this->Exit !== false)
					{
						$this->ThreadManager->StopThreads();

						Std::Out();
						Std::Out("[Info] [WhatsBot] Exiting ({$this->Exit})...");

						return $this->Exit;
					}

					# Reconnect if disconnected

					if(!$this->WhatsApp->IsConnected())
						$this->Connect(false);

					# Execute thread's tasks

					$this->ThreadManager->ExecuteTasks();
					
					# Listen

					$this->WhatsApp->PollMessage();

					# Ping & Reconnect

					if(time() >= $PingTimer + $PingDelay)
					{
						$this->WhatsApp->SendPing();
						$PingTimer = time();
					}

					if(time() >= $ReconnectTimer + $ReconnectDelay)
					{
						$this->Connect(false);
						$ReconnectTimer = time();
					}
				}
				catch(ConnectionException $Exception)
				{
					Std::Out();
					Std::Out('[Warning] [WhatsBot] Connection error (' . $Exception->GetMessage() . ')');

					$this->Connect(1);
				}
				catch(Exception $Exception)
				{
					Std::Out();
					Std::Out('[Warning] [WhatsBot] ' . get_class($Exception) . ' thrown while listening (' . $Exception->GetMessage() . ')');

					$this->Connect(1);
				}
			}
		}

		public function GetStartTime()
		{ return $this->StartTime; }

		public function _Exit($Code)
		{ $this->Exit = $Code; }
	}

	/* 
	 * To do: 
	 * 
	 * Flood protection
	 * https://github.com/mgp25/WhatsAPI-Official/issues/164#issuecomment-64821350
	 *  - Sync before send message. Sync if not in array. If disconnect reset array
	 * Fix #28 reconnect issue
	 * CLI only
	 * Implement WhatsApp workflow (https://github.com/mgp25/WhatsAPI-Official/wiki/WhatsAPI-Documentation#whatsapp-workflow)
	 * 
	 *****
	 * 
	 * Ideas: 
	 * 
	 * Detect lang from country code
	 * 
	 *****
	 * Questions: 
	 * 
	 * Send presence?
	 */