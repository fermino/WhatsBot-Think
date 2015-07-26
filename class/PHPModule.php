<?php
	require_once 'Lib/_Loader.php';

	require_once 'Module.php';

	class PHPModule extends Module
	{
		protected $PathExtension = 'php';
		protected $Extension = false;

		protected function _Load()
		{ return self::LOADED; }

		public function Execute(Message $Message, Array $Params = array())
		{
			if($Message->Time >= $this->WhatsBot->GetStartTime())
			{
				if($this->GetEnabled() === self::ENABLED)
				{
					if(is_readable($this->XPath))
					{
						$LangSection = "{$this->Key}_{$this->AliasOf}";

						$this->WhatsApp->SetLangSection($LangSection);
						$Lang = new Lang($LangSection);

						$ModuleManager = $this->ModuleManager;
						$WhatsBot = $this->WhatsBot;
						$WhatsApp = $this->WhatsApp;

						extract($Params);

						$Return = include $this->XPath;

						if($Return !== 1)
							return $Return;

						return self::EXECUTED;
					}

					Std::Out("[Warning] [Modules] Can't execute {$this->Key}::{$this->Name} ({$this->AliasOf}). {$this->PathExtension} file is not readable");

					return self::NOT_READABLE;
				}

				return self::NOT_ENABLED;
			}

			return self::EXECUTED;
		}
	}