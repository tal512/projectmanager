<?php

abstract class Model extends BaseClass
{
	protected $attributes;
	protected $rules;

	abstract public function save();
	abstract public function validate();
	abstract protected function setRules();

	public function __construct(&$container) {
		parent::__construct($container);
		$this->setRules();
	}

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
