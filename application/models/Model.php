<?php

abstract class Model extends BaseClass
{
	protected function loadDependencies() {
		require_once $this->config['app']['dir'] . '/models/Validator.php';
	}

	abstract public function save();
	abstract public function validate();
}
