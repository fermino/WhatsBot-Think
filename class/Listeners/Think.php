<?php
	require_once 'class/Lib/_Loader.php';

	require_once 'Core.php';

	require_once 'class/Think/Parser.php';

	class Think extends WhatsBotListenerCore
	{
		protected $ThinkParser = null;

		public function __construct(WhatsBot $WhatsBot, WhatsApp $WhatsApp, WhatsBotParser $Parser, ModuleManager $ModuleManager, ThreadManager $ThreadManager)
		{
			parent::__construct($WhatsBot, $WhatsApp, $Parser, $ModuleManager, $ThreadManager);

			$this->ThinkParser = new ThinkParser($this->WhatsApp);
		}

		public function onGetMessage($Me, $From, $ID, $Type, $Time, $Name, $Text)
		{
			$this->ThinkParser->Parse(new TextMessage($Me, $From, $From, $ID, $Type, $Time, $Name, $Text));
		}

		public function onGetGroupMessage($Me, $From, $User, $ID, $Type, $Time, $Name, $Text)
		{
			$this->ThinkParser->Parse(new TextMessage($Me, $From, $User, $ID, $Type, $Time, $Name, $Text));
		}
	}