<?php
	require_once 'Lib/_Loader.php';

	require_once 'ThreadTaskManager.php';

	abstract class WhatsBotThread extends Thread
	{
		use ThreadTaskManager;

		protected $Name = null;

		protected $Path = null;
		protected $JPath = null;
		protected $XPath = null;

		protected $PathExtension = null;

		protected $Extension = null;

		protected $Data = array();

		protected $Loaded = false;

		protected $Stop = false;

		const LOADED = true;
		const NOT_LOADED = false;
		const LOAD_ERROR = -1;

		final public function __construct($Name)
		{
			if(empty($this->PathExtension))
				trigger_error(get_class($this) . ' must redefine $PathExtension', E_USER_ERROR);

			if($this->Extension === null)
				trigger_error(get_class($this) . ' must redefine $Extension (as false if no extenison is used)', E_USER_ERROR);

			$this->Name = $Name;

			$this->Load();
		}

		final public function Load()
		{
			if(!is_string($this->Extension) || extension_loaded($this->Extension))
			{
				$this->Path = "class/Threads/{$this->Name}";

				$this->JPath = $this->Path . '.json';
				$this->XPath = $this->Path . '.' . $this->PathExtension;

				if(basename(dirname(realpath($this->JPath))) === 'Threads')
				{
					$Json = Json::Decode($this->JPath);

					if(is_array($Json))
					{
						if(is_readable($this->XPath))
						{
							// Lint

							$this->Data = $Json;

							if(isset($this->Data['Libs']) && is_array($this->Data['Libs']))
							{
								foreach($this->Data['Libs'] as $Lib)
								{
									if(!LoadLib($Lib))
									{
										Std::Out("[Warning] [Threads] Can't load {$this->Name}. {$Lib} lib doesn't exists");

										$this->Loaded = self::NOT_LOADED;

										return $this->Loaded;
									}
								}
							}

							$this->Loaded = $this->_Load();

							return $this->Loaded;
						}
						else
							Std::Out("[Warning] [Threads] Can't load {$this->Name}. {$this->PathExtension} file is not readable");
					}
					else
						Std::Out("[Warning] [Threads] Can't load {$this->Name}. Json file is not readable/decodeable");
				}
				else
					Std::Out("[Warning] [Threads] Can't load {$this->Name}. It is not in Threads folder");
			}
			else
				Std::Out("[Warning] [Threads] Can't load {$this->Name}. {$this->Extension} extension is not loaded");

			$this->Loaded = self::NOT_LOADED;

			return $this->Loaded;
		}

		abstract protected function _Load();

		final public function Run()
		{
			sleep(5);

			$this->LoadTaskManager();

			$this->Execute();

			Std::Out();
			Std::Out("[Info] [Threads] {$this->Name} stopped ($this->Stop)");

			if(strtolower(substr(PHP_OS, 0, 3)) === 'win') // When a thread gets stopped, windows says "PHP-CLI has stopped working *trollface*"
				while(true) // So ...
					sleep(1); // We will wait ... ... ...
		}

		abstract protected function Execute();

		public function IsLoaded()
		{ return $this->Loaded; }

		public function Stop($Code = 1)
		{ $this->Stop = $Code; }
	}