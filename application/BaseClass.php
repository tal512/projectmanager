<?php

abstract class BaseClass
{
	protected $config;
	protected $container;
	protected $db;

	public function __construct(&$container) {
		$this->config = $container['config'];
		$this->container = $container;
		$this->db = $container['db'];
		$this->loadDependencies();
	}

	public function camelCaseToUnderscore($value)
	{
		if (is_string($value)) {
			return strtolower(preg_replace('/(?<!^)([A-Z])/', '_$1', $value));
		} else {
			return '';
		}
	}

	public function underscoreToCamelCase($value)
	{
		if (is_string($value)) {
			return lcfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', $value))));
		} else {
			return '';
		}
	}

	abstract protected function loadDependencies();
}
