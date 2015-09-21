<?php
	require_once 'class/Lib/_Loader.php';

	require_once 'Core.php';

	class ConnectionListener extends WhatsBotListenerCore
	{
		protected function Load()
		{ }

		public function onPing($Me, $ID)
		{
			$this->WhatsApp->SendPong($ID);
		}
	}