<?php

abstract class Controller
{
	protected $container;

	public function __construct(&$container) {
		$this->container = $container;
		$this->loadDependencies();
	}

	abstract protected function loadDependencies();
}