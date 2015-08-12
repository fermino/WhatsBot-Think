<?php
	require_once 'class/Lib/_Loader.php';

	require_once 'LuaFunctions.php';

	class LuaWithPHP extends Lua
	{
		public function __construct($LuaScriptFile = null)
		{
			if($LuaScriptFile !== null)
				parent::__construct($LuaScriptFile);
			else
				parent::__construct();
			
			$Functions = array
			(
				'var_dump' => 'var_dump',
				'php_load_function' => array($this, 'php_load_function'),
				'php_load_class' => array($this, 'php_load_class'),
				'include' => array($this, 'include')
			);

			$this->RegisterCallbacks($Functions);
		}

		# Bridge

		public function php_load_class($ClassName, $ConstsWithPrefix = true, $MethodsWithPrefix = true, $PropertiesWithPrefix = true)
		{
			if(class_exists($ClassName))
				return $this->LinkObject($ClassName, $ConstsWithPrefix, $MethodsWithPrefix, $PropertiesWithPrefix);

			return false;
		}

		// php_init_class

		public function php_load_function($Function)
		{
			if(is_string($Function) && function_exists($Function))
				return $this->RegisterCallback($Function, $Function);

			return false;
		}

		# Variables

		public function AssignVariables(Array $Variables, $Prefix = null)
		{
			foreach($Variables as $Key => $Value)
				$this->AssignVariable($Prefix . $Key, $Value);
		}

		public function AssignVariable($Key, $Value)
		{
			if(is_scalar($Value))
			{
				if(is_object($this->Assign($Key, $Value)))
					return true;
			}
			elseif(is_array($Value))
			{
				if(is_object($this->Assign($Key, LuaFixArrayRecursive($Value))))
					return true;
			}

			Std::Out("[Warning] [Lua] Can't assign a non-scalar/array value (\${$Key}, " . gettype($Value) . ')');

			return false;
		}

		# Callbacks

		public function RegisterCallbacks(Array $Callbacks, $Prefix = null)
		{
			foreach($Callbacks as $Name => $Callback)
				$this->RegisterCallback($Prefix . $Name, $Callback);
		}

		public function RegisterCallback($Name, $Callback)
		{
			if(is_callable($Callback))
			{
				if(is_object(parent::RegisterCallback($Name, $Callback)))
					return true;
				else
					Std::Out("[Warning] [Lua] Can't register {$Name} callback");
			}
			else
				Std::Out("[Warning] [Lua] Can't register {$Name} callback. It is not callable");

			return false;
		}

		# Constants

		public function AssignUserConstants()
		{
			return $this->AssignVariables(get_defined_constants(true)['user']);
		}

		# Objects

		public function LinkObjects(Array $Objects, $ConstsWithPrefix = true, $MethodsWithPrefix = false, $PropertiesWithPrefix = false)
		{
			foreach($Objects as $Object)
				$this->LinkObject($Object, $ConstsWithPrefix, $MethodsWithPrefix, $PropertiesWithPrefix);
		}

		public function LinkObject($Object, $ConstsWithPrefix = true, $MethodsWithPrefix = false, $PropertiesWithPrefix = false)
		{
			# Reflection

			$Class = new ReflectionClass($Object);
			$ClassName = $Class->GetShortName();

			$Constants = $Class->GetConstants();
			$Methods = $Class->GetMethods(ReflectionMethod::IS_PUBLIC);
			$Properties = $Class->GetProperties(ReflectionProperty::IS_PUBLIC);

			# Prefixes

			$ClassPrefix = $ClassName . '_';

			$ConstsPrefix = $ConstsWithPrefix ? strtoupper($ClassPrefix) : null;
			$MethodsPrefix = $MethodsWithPrefix ? $ClassPrefix : null;
			$PropertiesPrefix = $PropertiesWithPrefix ? $ClassPrefix : null;

			# Consts

			$this->AssignVariables($Constants, $ConstsPrefix);

			# Callbacks

			/**
			 * If you can do this
			 * In one line
			 * Without using aux vars
			 * Without repeating code
			 * Without doing things like: asd($a = dsa(), ...)
			 * 
			 * YOU'RE A GOD. 
			 */

			// Convert ReflectionMethod's into array(MethodName => Callback, [...])

			$Methods = array_map(function($Method) { return $Method->name; }, $Methods);
			$Methods = array_combine($Methods, array_map(function($Method) use($Object) { return array($Object, $Method); }, $Methods));

			$Methods = array_filter($Methods, function($Method) { return strpos($Method, '__') !== 0; }, ARRAY_FILTER_USE_KEY);

			$this->RegisterCallbacks($Methods, $MethodsPrefix);

			# Vars

			// Convert ReflectionProperty's into array(PropertyName => Value, [...])

			$Properties = array_map(function($Property) { return $Property->name; }, $Properties);
			$Properties = array_combine($Properties, array_map(function($Property) use($Object) { return $Object->{$Property}; }, $Properties));

			$this->AssignVariables($Properties);

			return true;

			// Return consts && methods && properties (all of it)
		}
	}