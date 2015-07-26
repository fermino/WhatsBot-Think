<?php
	require_once 'MediaMessage.php';

	class VideoMessage extends MediaMessage
	{
		public $Duration = null;
		
		public $VCodec = null;
		public $ACodec = null;
		
		public $Preview = null;
		public $Caption = null;

		public function __construct($Me, $From, $User, $ID, $Type, $Time, $Name, $URL, $File, $Size, $MIME, $Hash, $Duration, $VCodec, $ACodec, $Preview, $Caption)
		{
			parent::__construct($Me, $From, $User, $ID, $Type, $Time, $Name, 'video', $URL, $File, $Size, $MIME, $Hash);

			$this->Duration = $Duration;
			$this->VCodec = $VCodec;
			$this->ACodec = $ACodec;
			$this->Preview = $Preview;
			$this->Caption = empty($Caption) ? null : $Caption;
		}
	}