<?php
	require_once 'class/Thread.php';

	const THREADMANAGER = 5;

	class ThreadManagerTasks
	{
		private $TaskManager = null;

		public function __construct(WhatsBotThread $TaskManager)
		{
			$this->TaskManager = $TaskManager;
		}

		public function WaitFor($Name, $UnsetBefore = true, $UnsetAfter = true)
		{
			return $this->TaskManager->WaitFor(THREADMANAGER, $Name, $UnsetBefore, $UnsetAfter);
		}

		public function LoadThreads()
		{ $this->TaskManager->AddTask(THREADMANAGER, 'LoadThreads', func_get_args()); }

		public function StartThreads()
		{ $this->TaskManager->AddTask(THREADMANAGER, 'StartThreads', func_get_args()); }

		public function StartThread($Name, $Show = true)
		{ $this->TaskManager->AddTask(THREADMANAGER, 'StartThread', func_get_args()); }

		public function StopThreads($Code = 'Exiting...')
		{ $this->TaskManager->AddTask(THREADMANAGER, 'StopThreads', func_get_args()); }

		public function StopThread($Name, $Code = 'Exiting...')
		{ $this->TaskManager->AddTask(THREADMANAGER, 'StopThread', func_get_args()); }

		// Load/Unload() ?
		// ExecuteTasks() ?
	}