<?php
	require_once 'class/Thread.php';

	const WHATSAPP = 2;

	class WhatsAppTasks
	{
		private $TaskManager = null;

		public function __construct(WhatsBotThread $TaskManager)
		{
			$this->TaskManager = $TaskManager;
		}

		public function WaitFor($Name, $UnsetBefore = true, $UnsetAfter = true)
		{
			return $this->TaskManager->WaitFor(WHATSAPP, $Name, $UnsetBefore, $UnsetAfter);
		}

		# User

		public function SetStatus($Message)
		{ $this->TaskManager->AddTask(WHATSAPP, 'SetStatus', func_get_args()); }

		public function SetProfilePicture($Path)
		{ $this->TaskManager->AddTask(WHATSAPP, 'SetProfilePicture', func_get_args()); }

		# Messages

		public function SetLangSection($Section)
		{ $this->TaskManager->AddTask(WHATSAPP, 'SetLangSection', func_get_args()); }

		public function SendMessage($To, $Key, $Pre = null)
		{ $this->TaskManager->AddTask(WHATSAPP, 'SendMessage', func_get_args()); }

		public function SendLangError($To, $Key, Array $Params = array())
		{ $this->TaskManager->AddTask(WHATSAPP, 'SendLangError', func_get_args()); }

		public function SendRawMessage($To, $Message)
		{ $this->TaskManager->AddTask(WHATSAPP, 'SendRawMessage', func_get_args()); }

		public function SendAudio($To, $Path, $StoreURLMedia = false, $Size = 0, $Hash = '')
		{ $this->TaskManager->AddTask(WHATSAPP, 'SendAudio', func_get_args()); }

		public function SendImage($To, $Path, $Caption = '', $StoreURLMedia = false, $Size = 0, $Hash = '')
		{ $this->TaskManager->AddTask(WHATSAPP, 'SendImage', func_get_args()); }

		public function SendVideo($To, $Path, $Caption = '', $StoreURLMedia = false, $Size = 0, $Hash = '')
		{ $this->TaskManager->AddTask(WHATSAPP, 'SendVideo', func_get_args()); }
	}