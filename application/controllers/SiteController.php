<?php

class SiteController extends Controller
{
	protected function loadDependencies() {}

	public function actionIndex()
	{
		die('site:index');
	}

	public function actionError($code, $message = 'missing')
	{
		die('site:error:code:' . $code . ':message:' . $message);
	}
}
