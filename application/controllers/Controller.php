<?php

abstract class Controller extends BaseClass
{
	protected function renderJson($data)
	{
		echo json_encode($data);
		die;
	}
}
