<?php
	require_once 'Lib/_Loader.php';

	require_once 'WhatsApp.php';

	require_once 'WhatsApp/TextMessage.php';
	require_once 'WhatsApp/MediaMessage.php';

	require_once 'ModuleManager.php';

	class WhatsBotParser
	{
		private $WhatsApp = null;

		private $ModuleManager = null;

		public function __construct(WhatsApp $WhatsApp, ModuleManager $ModuleManager)
		{
			$this->WhatsApp = $WhatsApp;
			$this->ModuleManager = $ModuleManager;
		}

		# Text

		public function ParseTextMessage(TextMessage $Message)
		{
			if(!empty($Message->Text))
			{
				if($Message->Text[0] === '!')
					return $this->ParseCommandMessage($Message);
				else
				{
					# Text Event
					
					$Module = $this->ModuleManager->GetModule('Event', 'text', false);

					if($Module instanceof Module)
						$this->SendResponse($Message, $Module->Execute($Message));
					elseif($Module !== Module::NOT_LOADED)
						$this->SendResponse($Message, $Module);
				}
			}

			foreach(Regex::MatchAll(Regex::URL, $Message->Text) as $URL)
				$this->ParseURLMessage($Message, $URL);

			/* Parser
			 * Send help if is pv
			 * AI?
			 */
		}

		private function ParseCommandMessage(TextMessage $Message)
		{
			$Parsed = explode(' ', substr($Message->Text, 1));

			if(!empty($Parsed[0]))
			{
				$Module = $this->ModuleManager->GetModule('Command', $Parsed[0], false);

				if($Module instanceof Module)
					$Response = $Module->Execute($Message, array('ModuleName' => $Parsed[0], 'Params' => $Parsed));
				else
					$Response = $Module;

				return $this->SendResponse($Message, $Response);
			}

			return null;
		}

		private function ParseURLMessage(TextMessage $Message, $URL)
		{
			$Domain = parse_url($URL, PHP_URL_HOST);
			$Extension = pathinfo(parse_url($URL, PHP_URL_PATH), PATHINFO_EXTENSION);

			# URL Event

			$Module = $this->ModuleManager->GetModule('Event', 'url', false);

			if($Module instanceof Module)
				$this->SendResponse($Message, $Module->Execute($Message, array('URL' => $URL, 'Domain' => $Domain, 'Extension' => $Extension)));
			elseif($Module !== Module::NOT_LOADED)
				$this->SendResponse($Message, $Module);

			# Try to execute Domain Module

			$Module = $this->ModuleManager->GetModule('Domain', $Domain, false);

			if($Module instanceof Module)
				return $this->SendResponse($Message, $Module->Execute($Message, array('URL' => $URL, 'Domain' => $Domain, 'Extension' => $Extension)));

			if($Module !== Module::NOT_LOADED)
				return $this->SendResponse($Message, $Module);

			# Try to execute Extension Module

			$Module = $this->ModuleManager->GetModule('Extension', $Extension, false);

			if($Module instanceof Module)
				return $this->SendResponse($Message, $Module->Execute($Message, array('URL' => $URL, 'Domain' => $Domain, 'Extension' => $Extension)));

			if($Module !== Module::NOT_LOADED)
				return $this->SendResponse($Message, $Module);

			return null;
		}

		# Media

		public function ParseMediaMessage(MediaMessage $Message)
		{
			$Module = $this->ModuleManager->GetModule('Media', $Message->SubType, false);

			if($Module instanceof Module)
				return $this->SendResponse($Message, $Module->Execute($Message));

			if($Module !== Module::NOT_LOADED)
				return $this->SendResponse($Message, $Module);

			return null;
		}

		private function SendResponse(Message $Message, $Code)
		{
			if($Code === Module::EXECUTED || $Code === floatval(Module::EXECUTED))
				return $Code;

			if(is_float($Code))
				$Code = intval($Code);

			if($Code === SEND_USAGE)
				$this->WhatsApp->SendMessage($Message->From, 'usage');
			else
			{
				$this->WhatsApp->SetLangSection('WhatsBot');

				if($Code === Module::NOT_LOADED)
					$this->WhatsApp->SendMessage($Message->From, 'message:module::not_loaded');
				elseif($Code === NOT_ADMIN)
					$this->WhatsApp->SendMessage($Message->From, 'message:not_admin');
				elseif($Code === Module::NOT_ENABLED)
					$this->WhatsApp->SendMessage($Message->From, 'message:module::not_enabled');
				elseif($Code === INTERNAL_ERROR || $Code === Module::NOT_READABLE)
					$this->WhatsApp->SendMessage($Message->From, 'message:internal_error');
				elseif($Code === Module::LOAD_ERROR)
					$this->WhatsApp->SendMessage($Message->From, 'message:module::load_error');
				elseif(is_array($Code) && !empty($Code[1])) // Some other
					$this->WhatsApp->SendLangError($Message->From, $Code[1]);
				else
				{
					$Key = array_search($Code, get_defined_constants(true)['user'], true);

					if($Key !== false)
						$Key = "const {$Key} = ";

					Std::Out("[Warning] [Parser] Wrong response code ({$Key}" . var_export($Code, true) . "). Message from {$Message->From} ({$Message->ID})");

					$this->WhatsApp->SendMessage($Message->From, 'message:internal_error:wrong_response_code', $Code);
				}
			}

			return $Code;
		}
	}