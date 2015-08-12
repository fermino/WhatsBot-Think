<?php
	require_once 'Lib/_Loader.php';

	require_once 'ModuleManager.php';
	require_once 'WhatsBot.php';
	require_once 'WhatsApp.php';

	const SEND_USAGE = 2;

	const INTERNAL_ERROR = -1;
	const LANG_ERROR = -3;
	const NOT_ADMIN = -4;

	abstract class Module
	{
		protected $ModuleManager = null;
		protected $WhatsBot = null;
		protected $WhatsApp = null;

		protected $Key = null;

		protected $Name = null;
		protected $AliasOf = null;

		protected $Path = null;
		protected $JPath = null;
		protected $XPath = null;

		protected $PathExtension = null;

		protected $Extension = null;

		protected $Data = null;

		protected $Loaded = false;
		protected $Enabled = true;

		const LOADED = true;
		const NOT_LOADED = false;
		const LOAD_ERROR = -5;

		const ENABLED = true;
		const NOT_ENABLED = -6;

		const NOT_READABLE = -2;

		const EXECUTED = true;

		final public function __construct(ModuleManager $ModuleManager, WhatsBot $WhatsBot, WhatsApp $WhatsApp, $Key, $Name, $AliasOf)
		{
			if(empty($this->PathExtension))
				trigger_error(get_class($this) . ' must redefine $PathExtension', E_USER_ERROR);

			if($this->Extension === null)
				trigger_error(get_class($this) . ' must redefine $Extension (as false if no extenison is used)', E_USER_ERROR);

			$this->ModuleManager = $ModuleManager;
			$this->WhatsBot = $WhatsBot;
			$this->WhatsApp = $WhatsApp;

			$this->Key = $Key;

			$this->Name = $Name;
			$this->AliasOf = $AliasOf;

			$this->Load();
		}

		final public function Load()
		{
			if(!is_string($this->Extension) || extension_loaded($this->Extension))
			{
				if($this->ModuleManager->KeyExists($this->Key))
				{
					$this->Path = "class/Modules/{$this->Key}_{$this->AliasOf}";

					$this->JPath = $this->Path . '.json';
					$this->XPath = $this->Path . '.' . $this->PathExtension;

					if(basename(dirname(realpath($this->JPath))) === 'Modules')
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
											Std::Out("[Warning] [Modules] Can't load {$this->Key}::{$this->Name} ({$this->AliasOf}). {$Lib} lib doesn't exists");
											
											$this->Loaded = self::NOT_LOADED;

											return $this->Loaded;
										}
									}
								}

								$this->Loaded = $this->_Load();
	
								return $this->Loaded;
							}
							else
								Std::Out("[Warning] [Modules] Can't load {$this->Key}::{$this->Name} ({$this->AliasOf}). {$this->PathExtension} file is not readable");
						}
						else
							Std::Out("[Warning] [Modules] Can't load {$this->Key}::{$this->Name} ({$this->AliasOf}). Json file is not decodeable");
					}
					else
						Std::Out("[Warning] [Modules] Can't load {$this->Key}::{$this->Name} ({$this->AliasOf}). It is not in Modules folder");
				}
				else
					Std::Out("[Warning] [Modules] Can't load {$this->Key}::{$this->Name} ({$this->AliasOf}). That key doesn't exists");
			}
			else
				Std::Out("[Warning] [Modules] Can't load {$this->Key}::{$this->Name} ({$this->AliasOf}). {$this->Extension} extension is not loaded");

			$this->Loaded = self::NOT_LOADED;

			return $this->Loaded;
		}

		abstract protected function _Load();

		abstract public function Execute(Message $Message, Array $Params = array());

		public function IsAlias()
		{ return $this->Name != $this->AliasOf; }

		public function IsLoaded()
		{ return $this->Loaded; }

		public function GetEnabled()
		{ return $this->Enabled; }

		public function Enable()
		{ $this->Enabled = self::ENABLED; }

		public function Disable()
		{ $this->Enabled = self::NOT_ENABLED; }

		public function GetData()
		{ return $this->Data; }
	}