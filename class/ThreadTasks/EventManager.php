<?php
	require_once 'class/Thread.php';

	const EVENTMANAGER = 3;

	class EventManagerTasks
	{
		private $TaskManager = null;

		public function __construct(WhatsBotThread $TaskManager)
		{
			$this->TaskManager = $TaskManager;
		}

		public function WaitFor($Name, $UnsetBefore = true, $UnsetAfter = true)
		{
			return $this->TaskManager->WaitFor(EVENTMANAGER, $Name, $UnsetBefore, $UnsetAfter);
		}

		public function BindListener($Listener, $Key = null)
		{ $this->TaskManager->AddTask(EVENTMANAGER, 'BindListener', func_get_args()); }

		public function UnbindListener($Key)
		{ $this->TaskManager->AddTask(EVENTMANAGER, 'UnbindListener', func_get_args()); }

		public function EnableListener($Key)
		{ $this->TaskManager->AddTask(EVENTMANAGER, 'EnableListener', func_get_args()); }

		public function DisableListener($Key)
		{ $this->TaskManager->AddTask(EVENTMANAGER, 'DisableListener', func_get_args()); }

		public function Fire($Event, Array $Params = array())
		{ $this->TaskManager->AddTask(EVENTMANAGER, 'Fire', func_get_args()); }
	}