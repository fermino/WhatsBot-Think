<?php
	require_once 'MediaMessage.php';

	class VideoMessage extends MediaMessage
	{
		public $Duration = null;
		
		public $Preview = null;
		public $Caption = null;

		public $Width = null;
		public $Height = null;

		public $FPS = null;

		public $VCodec = null;
		public $VBitRate = null;

		public $ACodec = null;
		public $ABitRate = null;
		public $ASampleFrequency = null;
		public $ASampleFormat = null;

		public function __construct($Me, $From, $User, $ID, $Type, $Time, $Name, $URL, $File, $Size, $MIME, $Hash, $Duration, $Preview, $Caption, $Width, $Height, $FPS, $VCodec, $VBitRate, $ACodec, $ABitRate, $ASampleFrequency, $ASampleFormat)
		{
			parent::__construct($Me, $From, $User, $ID, $Type, $Time, $Name, 'video', $URL, $File, $Size, $MIME, $Hash);

			$this->Duration = (int) $Duration;
			$this->Preview = $Preview;
			$this->Caption = empty($Caption) ? null : $Caption;
			$this->Width = (int) $Width;
			$this->Height = (int) $Height;
			$this->FPS = (int) $FPS;
			$this->VCodec = $VCodec;
			$this->VBitRate = (int) $VBitRate;
			$this->ACodec = $ACodec;
			$this->ABitRate = (int) $ABitRate;
			$this->ASampleFrequency = (int) $ASampleFrequency;
			$this->ASampleFormat = $ASampleFormat;
		}
	}