<?php
	require_once 'MediaMessage.php';

	class ImageMessage extends MediaMessage
	{
		public $Width = null;
		public $Height = null;

		public $Preview = null;
		public $Caption = null;

		public function __construct($Me, $From, $User, $ID, $Type, $Time, $Name, $URL, $File, $Size, $MIME, $Hash, $Width, $Height, $Preview, $Caption)
		{
			parent::__construct($Me, $From, $User, $ID, $Type, $Time, $Name, 'image', $URL, $File, $Size, $MIME, $Hash);

			$this->Width = $Width;
			$this->Height = $Height;
			$this->Preview = $Preview;
			$this->Caption = empty($Caption) ? null : $Caption;
		}
	}