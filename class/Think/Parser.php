<?php
	require_once 'class/Lib/_Loader.php';

	require_once 'class/WhatsBot.php';

	require_once 'class/WhatsApp.php';

	require_once 'class/WhatsApp/TextMessage.php';

	class ThinkParser
	{
		private $WhatsBot = null;

		private $WhatsApp = null;

		private $PDO = null;

		public function __construct(WhatsBot $WhatsBot, WhatsApp $WhatsApp)
		{
			$this->WhatsBot = $WhatsBot;
			$this->WhatsApp = $WhatsApp;

			if(!is_readable('data/Think.sqlite'))
				$this->WhatsBot->_Exit('You must create an empty database. Go to data/ and execute create_think.php');
			else
				$this->PDO = new PDO('sqlite:data/Think.sqlite');
		}

		public function Parse(TextMessage $Message)
		{
			if(!empty($Message->Text))
			{
				$this->WhatsApp->SetLangSection('Think');
				
				if(!$Message->IsGroupMessage() && $Message->Text[0] !== '.') // Avoid character hardcoding
				{
					$Statement = $this->PDO->prepare('INSERT INTO `thinks` (`id`, `from`, `mid`, `time`, `name`, `think`) VALUES (NULL, :from, :mid, :time, :name, :think)');

					$Statement->bindParam(':from', $Message->From, PDO::PARAM_STR);
					$Statement->bindParam(':mid', $Message->ID, PDO::PARAM_STR);
					$Statement->bindParam(':time', $Message->Time, PDO::PARAM_INT);
					$Statement->bindParam(':name', $Message->Name, PDO::PARAM_STR);
					$Statement->bindParam(':think', $Message->Text, PDO::PARAM_STR);

					$Response = $Statement->execute();

					if($Response)
					{
						$this->WhatsApp->SendMessage($Message->From, 'message:think_saved');
						$this->WhatsApp->SendMessage($Message->From, 'help');
					}
					else
					{
						$this->WhatsApp->SendMessage($Message->From, 'message:think_save_failure');

						Std::Out();
						Std::Out('[Warning] [ThinkParser] Think save failure. Dumping...');

						Std::Out('TextMessage (');
						foreach(get_object_vars($Message) as $Key => $Value)
							Std::Out("\t'{$Key}' => " . var_export($Value, true));
						Std::Out(')');
					}
					
				}
				elseif($Message->Text == '.')
				{
					$Response = $this->PDO->query('SELECT `think` FROM `thinks` ORDER BY RANDOM() LIMIT 1');

					if($Response !== false)
					{
						$Think = $Response->fetch();

						if($Think === false)
						{
							$this->WhatsApp->SendMessage($Message->From, 'message:think_empty_db');
							$this->WhatsApp->SendMessage($Message->From, 'help');
						}
						else
							$this->WhatsApp->SendRawMessage($Message->From, $Think['think']);
					}
					else
					{
						$this->WhatsApp->SendMessage($Message->From, 'message:think_get_failure');

						Std::Out();
						Std::Out('[Warning] [ThinkParser] Think get failure');
					}
				}
			}
		}
	}