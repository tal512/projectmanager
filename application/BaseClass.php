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

	/**
	 * Recommended way to get Table name for model, as it injects the config's
	 * table prefix
	 */
	public function getTable($model)
	{
		return $model::getTableName($this->config['db']['prefix']);
	}

	abstract protected function loadDependencies();
}
