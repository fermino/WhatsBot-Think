<?php
	# Config

	$File = 'Think_empty.sqlite';
	$Rename = 'Think.sqlite';
	$Source = 'create_think.sql';

	# Create

	error_reporting(E_ALL);

	echo "Creating {$File} from {$Source}..." . PHP_EOL . PHP_EOL;

	$Source = file_get_contents($Source);

	if(strlen($Source) > 0)
	{
		$PDO = new PDO("sqlite:{$File}");

		if($PDO->exec($Source) === 0)
			echo 'Success!' . PHP_EOL . PHP_EOL . "==>\tDon't forget to rename it to {$Rename}" . PHP_EOL;
		else
			echo 'Failure!';
	}
	else
		echo "Source file doesn't exists";
