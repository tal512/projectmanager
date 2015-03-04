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

	abstract protected function loadDependencies();
}
