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

	public function loadPost()
	{
		$post = [];
		foreach (array_keys($rules) as $attribute) {
			if (isset($_POST[$attribute])) {
				$attribute = $_POST[$attribute];
				$validator = $rules[$attribute];
				$this->$attribute = Validator::$validator($attribute);
				$post[$attribute] = $this->$attribute;
			}
		}
		return $post;
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
