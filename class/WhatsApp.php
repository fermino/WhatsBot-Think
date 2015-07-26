<?php
	require_once 'Lib/_Loader.php';

	require_once 'WhatsAPI/whatsprot.class.php';

	class WhatsApp
	{
		private $WhatsApp = null;

		private $LangSection = null;

		public function __construct(WhatsProt $WhatsApp, $LangSection = null)
		{
			$this->WhatsApp = $WhatsApp;

			$this->LangSection = $LangSection;
		}

		# Config

		public function EventManager()
		{ return $this->WhatsApp->EventManager(); }

		# Consts

		const MaxMediaSize = 16777216;

		# Connection

		public function Connect()
		{ return $this->WhatsApp->Connect(); }

		public function Disconnect()
		{ return $this->WhatsApp->Disconnect(); }

		public function IsConnected()
		{ return $this->WhatsApp->IsConnected(); }


		public function SendPing()
		{ return $this->WhatsApp->SendPing(); }

		public function SendPong($ID)
		{ return $this->WhatsApp->SendPong($ID); }
		
		# Login

		public function LoginWithPassword($Password)
		{ return $this->WhatsApp->LoginWithPassword($Password); }

		# Listen

		public function PollMessage($AutoReceipt = true)
		{ return $this->WhatsApp->PollMessage($AutoReceipt); }

		# User

		public function SetStatus($Message)
		{ return $this->WhatsApp->SendStatusUpdate($Message); }

		public function SetProfilePicture($Path)
		{ return $this->WhatsApp->SendSetProfilePicture($Path); }

		# Messages

		public function SetLangSection($Section)
		{ $this->LangSection = $Section; }

		public function SendMessage($To, $Key, $Pre = null)
		{
			$Args = func_get_args();
			array_shift($Args);

			$Message = call_user_func_array(array(new Lang($this->LangSection), 'Get'), $Args);

			if($Message !== false)
				return $this->SendRawMessage($To, (is_array($Pre) && !empty($Pre[0]) ? $Pre[0] : null) . $Message);
			else
			{
				if($Key === 'message:module::not_loaded')
					return $this->SendRawMessage($To, 'That module doesn\'t exists. Try !help to see a list of available modules');
				elseif($Key === 'message:not_admin')
					return $this->SendRawMessage($To, 'You need admin rights in order to do that');
				elseif($Key === 'message:module::not_enabled')
					return $this->SendRawMessage($To, 'That module is loaded, but is disabled. Ask the admin about it');
				elseif($Key === 'message:internal_error')
					return $this->SendRawMessage($To, 'Internal error');
				elseif($Key === 'message:internal_error:wrong_response_code')
					return $this->SendRawMessage($To, 'Internal error. The code (' . var_export($Pre, true) . ') returned by the module is wrong');
				elseif($Key === 'message:module::load_error')
					return $this->SendRawMessage($To, 'That module is loaded, but there are some troubles (at reload, json-php not readable/lint). If you are the admin, see the logs!');
				else
				{
					array_shift($Args);

					return $this->SendLangError($To, $Key, $Args);
				}
			}
		}

		public function SendLangError($To, $Key, Array $Params = array())
		{
			$String = '';

			foreach($Params as $Param)
				$String .= var_export($Param, true) . ', ';

			$String = substr($String, 0, strlen($String) - 2);

			return $this->SendRawMessage($To, "Lang error. Key not found: \n{$this->LangSection}::{$Key}({$String})");
		}

		public function SendRawMessage($To, $Message)
		{ // Validate from
		  // Send composing
			return $this->WhatsApp->SendMessage($To, $Message);
		}

		public function SendAudio($To, $Path, $StoreURLMedia = false, $Size = 0, $Hash = '')
		{ return $this->WhatsApp->SendMessageAudio($To, $Path, $StoreURLMedia, $Size, $Hash); }

		public function SendImage($To, $Path, $Caption = '', $StoreURLMedia = false, $Size = 0, $Hash = '')
		{ return $this->WhatsApp->SendMessageImage($To, $Path, $StoreURLMedia, $Size, $Hash, $Caption); }

		public function SendVideo($To, $Path, $Caption = '', $StoreURLMedia = false, $Size = 0, $Hash = '')
		{ return $this->WhatsApp->SendMessageVideo($To, $Path, $StoreURLMedia, $Size, $Hash, $Caption); }

		/* Functions: 
		 * checkCredentials()
		 * codeRegister($code)
		 * codeRequest($method = 'sms', $countryCode = null, $langCode = null)
		 * getMessages()
		 * sendActiveStatus()
		 * sendBroadcastAudio($targets, $path, $storeURLmedia = false, $fsize = 0, $fhash = "")
		 * sendBroadcastImage($targets, $path, $storeURLmedia = false, $fsize = 0, $fhash = "", $caption = "")
		 * sendGetBroadcastLists()
		 * sendBroadcastLocation($targets, $long, $lat, $name = null, $url = null)
		 * sendBroadcastMessage($targets, $message)
		 * sendBroadcastVideo($targets, $path, $storeURLmedia = false, $fsize = 0, $fhash = "", $caption = "")
		 * sendClearDirty($categories)
		 * sendGetClientConfig()
		 * sendGetGroupV2Info 
		 * sendGetGroupsInfo($gjid)
		 * sendGetGroupsOwning()
		 * sendGetGroupsParticipants($gjid)
		 * sendGetNormalizedJid($countryCode, $number)
		 * sendGetPrivacyBlockedList()
		 * sendGetProfilePicture($number, $large = false)
		 * sendGetRequestLastSeen($to)
		 * sendGetServerProperties()
		 * sendGetServicePricing($lg, $lc)
		 * sendGetStatuses($jids)
		 * sendGroupsChatCreate($subject, $participants = array())
		 * sendSetGroupSubject($gjid, $subject)
		 * sendGroupsChatEnd($gjid)
		 * sendGroupsLeave($gjids)
		 * sendGroupsParticipantsAdd($groupId, $participants)
		 * sendGroupsParticipantsRemove($groupId, $participants)
		 * sendMessageComposing($to)
		 * sendMessagePaused($to)
		 * sendMessageLocation($to, $long, $lat, $name = null, $url = null)
		 * sendChatState($to, $state)
		 * sendNextMessage()
		 * sendOfflineStatus()
		 * sendPong($msgid)
		 * sendAvailableForChat($nickname = null)
		 * sendPresence($type = "active")
		 * sendPresenceSubscription($to)
		 * sendSetGroupPicture($gjid, $path)
		 * sendSetPrivacyBlockedList($blockedJids = array())
		 * sendSetRecoveryToken($token)
		 * sendVcard($to, $name, $vCard)
		 * sendBroadcastVcard($targets, $name, $vCard)
		 */
	}