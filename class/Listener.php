<?php
	require_once 'Lib/_Loader.php';

	require_once 'WhatsApp.php';

	require_once 'WhatsApp/TextMessage.php';
	require_once 'WhatsApp/AudioMessage.php';
	require_once 'WhatsApp/ImageMessage.php';
	require_once 'WhatsApp/VideoMessage.php';

	require_once 'Parser.php';

	class WhatsBotListener
	{
		private $WhatsApp = null;

		private $Parser = null;

		public function __construct(WhatsApp $WhatsApp, WhatsBotParser $Parser)
		{
			$this->WhatsApp = $WhatsApp;

			$this->Parser = $Parser;
		}

		# Connection

		public function onPing($Me, $ID)
		{
			$this->WhatsApp->SendPong($ID);
		}

		############
		# Messages #
		############

		# Text

		public function onGetMessage($Me, $From, $ID, $Type, $Time, $Name, $Text)
		{
			$this->Parser->ParseTextMessage(new TextMessage($Me, $From, $From, $ID, $Type, $Time, $Name, $Text));
		}

		public function onGetGroupMessage($Me, $From, $User, $ID, $Type, $Time, $Name, $Text)
		{
			$this->Parser->ParseTextMessage(new TextMessage($Me, $From, $User, $ID, $Type, $Time, $Name, $Text));
		}

		# Media

		public function onGetAudio($Me, $From, $ID, $Type, $Time, $Name, $Size, $URL, $File, $MIME, $Hash, $Duration, $Codec, $User = null)
		{
			$User = $User == null ? $From : $User;

			$this->Parser->ParseMediaMessage(new AudioMessage($Me, $From, $User, $ID, $Type, $Time, $Name, $URL, $File, $Size, $MIME, $Hash, $Duration, $Codec));
		}

		public function onGetImage($Me, $From, $ID, $Type, $Time, $Name, $Size, $URL, $File, $MIME, $Hash, $Width, $Height, $Preview, $Caption)
		{
			$this->Parser->ParseMediaMessage(new ImageMessage($Me, $From, $From, $ID, $Type, $Time, $Name, $URL, $File, $Size, $MIME, $Hash, $Width, $Height, $Preview, $Caption));
		}

		public function onGetGroupImage($Me, $From, $User, $ID, $Type, $Time, $Name, $Size, $URL, $File, $MIME, $Hash, $Width, $Height, $Preview, $Caption)
		{
			$this->Parser->ParseMediaMessage(new ImageMessage($Me, $From, $User, $ID, $Type, $Time, $Name, $URL, $File, $Size, $MIME, $Hash, $Width, $Height, $Preview, $Caption));
		}

		public function onGetVideo($Me, $From, $ID, $Type, $Time, $Name, $URL, $File, $Size, $MIME, $Hash, $Duration, $VCodec, $ACodec, $Preview, $Caption)
		{
			$this->Parser->ParseMediaMessage(new VideoMessage($Me, $From, $From, $ID, $Type, $Time, $Name, $URL, $File, $Size, $MIME, $Hash, $Duration, $VCodec, $ACodec, $Preview, $Caption));
		}

		public function onGetGroupVideo($Me, $From, $User, $ID, $Type, $Time, $Name, $URL, $File, $Size, $MIME, $Hash, $Duration, $VCodec, $ACodec, $Preview, $Caption)
		{
			$this->Parser->ParseMediaMessage(new VideoMessage($Me, $From, $User, $ID, $Type, $Time, $Name, $URL, $File, $Size, $MIME, $Hash, $Duration, $VCodec, $ACodec, $Preview, $Caption));
		}

		// Add onGetLocation & onGetvCard
		// Message logging

		/* Events: 
		 * public function onClose($mynumber, $error) {}
	     * public function onCodeRegister($mynumber, $login, $password, $type, $expiration, $kind, $price, $cost, $currency, $price_expiration) {}
	     * public function onCodeRegisterFailed($mynumber, $status, $reason, $retry_after) {}
	     * public function onCodeRequest($mynumber, $method, $length) {}
	     * public function onCodeRequestFailed($mynumber, $method, $reason, $param) {}
	     * public function onCodeRequestFailedTooRecent($mynumber, $method, $reason, $retry_after) {}
	     * public function onCodeRequestFailedTooManyGuesses($mynumber, $method, $reason, $retry_after) {}
	     * public function onConnect($mynumber, $socket) {}
	     * public function onConnectError($mynumber, $socket) {}
	     * public function onCredentialsBad($mynumber, $status, $reason) {}
	     * public function onCredentialsGood($mynumber, $login, $password, $type, $expiration, $kind, $price, $cost, $currency, $price_expiration) {}
	     * public function onDisconnect($mynumber, $socket) {}
	     * public function onDissectPhone($mynumber, $phonecountry, $phonecc, $phone, $phonemcc, $phoneISO3166, $phoneISO639, $phonemnc) {}
	     * public function onDissectPhoneFailed($mynumber) {}
	     * public function onGetBroadcastLists($mynumber, $broadcastLists){}
	     * public function onGetError($mynumber, $from, $id, $data) {}
	     * public function onGetExtendAccount($mynumber, $kind, $status, $creation, $expiration) {}
	     * public function onGetFeature($mynumber, $from, $encrypt) {}
	     * public function onGetGroupParticipants($mynumber, $groupId, $groupList) {}
	     * public function onGetGroups($mynumber, $groupList) {}
	     * public function onGetGroupsInfo($mynumber, $groupList) {}
	     * public function onGetGroupV2Info( $mynumber, $group_id, $creator, $creation, $subject, $participants, $admins, $fromGetGroup ){}
	     * public function onGetGroupsSubject($mynumber, $group_jid, $time, $author, $name, $subject) {}
	     * public function onGetKeysLeft($mynumber, $keysLeft) {}
	     * public function onGetLocation($mynumber, $from, $id, $type, $time, $name, $author, $longitude, $latitude, $url, $preview, $fromJID_ifGroup = null) {}
	     * public function onGetNormalizedJid($mynumber, $data) {}
	     * public function onGetPrivacyBlockedList($mynumber, $data) {}
	     * public function onGetProfilePicture($mynumber, $from, $type, $data) {}
	     * public function onGetReceipt($from, $id, $offline, $retry) {}
	     * public function onGetRequestLastSeen($mynumber, $from, $id, $seconds) {}
	     * public function onGetServerProperties($mynumber, $version, $props) {}
	     * public function onGetServicePricing($mynumber, $price, $cost, $currency, $expiration) {}
	     * public function onGetStatus($mynumber, $from, $requested, $id, $time, $data) {}
	     * public function onGetSyncResult($result) {}
	     * public function onGetvCard($mynumber, $from, $id, $type, $time, $name, $vcardname, $vcard, $fromJID_ifGroup = null) {}
	     * public function onGroupCreate($mynumber, $groupId) {}
	     * public function onGroupisCreated($mynumber, $creator, $gid, $subject, $admin, $creation, $members = array()) {}
	     * public function onGroupsChatCreate($mynumber, $gid) {}
	     * public function onGroupsChatEnd($mynumber, $gid) {}
	     * public function onGroupsParticipantsAdd($mynumber, $groupId, $jid) {}
	     * public function onGroupsParticipantsPromote($myNumber, $groupJID, $time, $issuerJID, $issuerName, $promotedJIDs = array()) {}
	     * public function onGroupsParticipantsRemove($mynumber, $groupId, $jid) {}
	     * public function onLogin($mynumber) {}
	     * public function onLoginFailed($mynumber, $data) {}
	     * public function onLoginSuccess($mynumber, $kind, $status, $creation, $expiration) {}
	     * public function onAccountExpired($mynumber, $kind, $status, $creation, $expiration ){}
	     * public function onMediaMessageSent($mynumber, $to, $id, $filetype, $url, $filename, $filesize, $filehash, $caption, $icon) {}
	     * public function onMediaUploadFailed($mynumber, $id, $node, $messageNode, $statusMessage) {}
	     * public function onMessageComposing($mynumber, $from, $id, $type, $time) {}
	     * public function onMessagePaused($mynumber, $from, $id, $type, $time) {}
	     * public function onMessageReceivedClient($mynumber, $from, $id, $type, $time, $participant) {}
	     * public function onMessageReceivedServer($mynumber, $from, $id, $type, $time) {}
	     * public function onPaidAccount($mynumber, $author, $kind, $status, $creation, $expiration) {}
	     * public function onPaymentRecieved($mynumber, $kind, $status, $creation, $expiration) {}
	     * public function onPresenceAvailable($mynumber, $from) {}
	     * public function onPresenceUnavailable($mynumber, $from, $last) {}
	     * public function onProfilePictureChanged($mynumber, $from, $id, $time) {}
	     * public function onProfilePictureDeleted($mynumber, $from, $id, $time) {}
	     * public function onSendMessage($mynumber, $target, $messageId, $node) {}
	     * public function onSendMessageReceived($mynumber, $id, $from, $type) {}
	     * public function onSendPong($mynumber, $msgid) {}
	     * public function onSendPresence($mynumber, $type, $name ) {}
	     * public function onSendStatusUpdate($mynumber, $txt ) {}
	     * public function onStreamError($data) {}
	     * public function onUploadFile($mynumber, $filename, $url) {}
	     * public function onUploadFileFailed($mynumber, $filename) {}
		 */
	}