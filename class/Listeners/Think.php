<?php
	require_once 'class/Lib/_Loader.php';

	require_once 'Core.php';

	require_once 'class/Think/Parser.php';

	class ThinkListener extends WhatsBotListenerCore
	{
		protected $ThinkParser = null;

		protected function Load()
		{
			$this->ThinkParser = new ThinkParser($this->WhatsBot, $this->WhatsApp);
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