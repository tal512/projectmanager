<?php

abstract class Controller extends BaseClass
{
	protected function loadDependencies()
	{
		require_once $this->config['app']['dir'] . '/models/Validator.php';
	}

	protected function renderJson($data)
	{
		echo json_encode($data);
		die;
	}
}
