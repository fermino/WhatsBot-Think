<?php
	class Message
	{
		public $Me = null;

		public $From = null;
		public $User = null;

		public $ID = null;
		public $Type = null;
		public $Time = null;
		public $Name = null;

		public function __construct($Me, $From, $User, $ID, $Type, $Time, $Name)
		{
			$this->Me = $Me;
			$this->From = $From;
			$this->User = $User;
			$this->ID = $ID;
			$this->Type = $Type;
			$this->Time = (int) $Time;
			$this->Name = $Name;
		}

		public function IsGroupMessage()
		{
			return $this->From !== $this->User;
		}
	}