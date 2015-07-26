<?php
	require_once 'Lib/_Loader.php';

	require_once 'ModuleManagerExists.php';
	require_once 'ModuleManagerGetter.php';
	require_once 'ModuleManagerLoader.php';

	require_once 'WhatsBot.php';
	require_once 'WhatsApp.php';

	class ModuleManager
	{
		use ModuleManagerExists;
		use ModuleManagerGetter;
		use ModuleManagerLoader;

		private $WhatsBot = null;
		private $WhatsApp = null;

		private $Modules = array
		( // What if we don't create this ? Traits should be protected (getter/setter?)
			'Command' => array(),
			'Domain' => array(),
			'Extension' => array(),
			'Media' => array(),
			'_' => array()
		);

		public function __construct(WhatsBot $WhatsBot, WhatsApp $WhatsApp)
		{
			$this->WhatsBot = $WhatsBot;
			$this->WhatsApp = $WhatsApp;
		}
	}