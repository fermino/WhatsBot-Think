<?php
	require_once 'MediaMessage.php';

	class AudioMessage extends MediaMessage
	{
		public $Duration = null;

		public $Codec = null;

		public function __construct($Me, $From, $User, $ID, $Type, $Time, $Name, $URL, $File, $Size, $MIME, $Hash, $Duration, $Codec)
		{
			parent::__construct($Me, $From, $User, $ID, $Type, $Time, $Name, 'audio', $URL, $File, $Size, $MIME, $Hash);

			$this->Duration = $Duration;
			$this->Codec = $Codec;
		}
	}
	