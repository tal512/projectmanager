<?php

abstract class Model extends BaseClass
{
	protected $attributes;

	abstract public function save();
	abstract public function validate();

	protected function loadDependencies() {
		require_once $this->config['app']['dir'] . '/models/Validator.php';
	}

	public function __get($name)
	{
		return $this->attributes[$name];
	}

	public function __set($name, $value)
	{
		$this->attributes[$name] = $value;
	}
}
