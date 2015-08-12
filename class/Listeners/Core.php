<?php
	require_once 'class/Lib/_Loader.php';

	require_once 'class/WhatsBot.php';

	require_once 'class/WhatsApp.php';

	require_once 'class/WhatsApp/TextMessage.php';
	require_once 'class/WhatsApp/AudioMessage.php';
	require_once 'class/WhatsApp/ImageMessage.php';
	require_once 'class/WhatsApp/VideoMessage.php';

	require_once 'class/Parser.php';

	require_once 'class/ModuleManager.php';
	require_once 'class/ThreadManager.php';

	abstract class WhatsBotListenerCore
	{
		protected $WhatsBot = null;

		protected $WhatsApp = null;

		protected $Parser = null;

		protected $ModuleManager = null;
		protected $ThreadManager = null;

		public function __construct(WhatsBot $WhatsBot, WhatsApp $WhatsApp, WhatsBotParser $Parser, ModuleManager $ModuleManager, ThreadManager $ThreadManager)
		{
			$this->WhatsBot = $WhatsBot;
			$this->WhatsApp = $WhatsApp;
			$this->Parser = $Parser;
			$this->ModuleManager = $ModuleManager;
			$this->ThreadManager = $ThreadManager;

			$this->Load();
		}

		abstract protected function Load();
	}