<?php

class RequestHandler
{
	private $baseDir;

	private $container;

	private $defaultController;
	private $defaultAction;
	private $defaultParams;

	private $errorController;
	private $errorAction;

	public function __construct(&$container, $config)
	{
		$this->baseDir = '';
		$this->container = $container;

		$this->defaultController = $config['defaultController'];
		$this->defaultAction = $config['defaultAction'];
		$this->defaultParams = array();

		$this->errorController = $config['errorController'];
		$this->errorAction = $config['errorAction'];

		$this->handle();
	}

	protected function handle()
	{
		$url = $this->parseUrl();
		$request = $this->parseRequest($url);
		$requestParams = $this->parseRequestParams($request);

		$this->loadController($requestParams);
	}

	protected function parseUrl()
	{
		if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] && !in_array(strtolower($_SERVER['HTTPS']), array('on', 'off'))) {
			$url = 'https://';
		} else {
			$url = 'http://';
		}

		$url .= $_SERVER['HTTP_HOST'];
		$url .= $_SERVER['REQUEST_URI'];

		return $url;
	}

	protected function parseRequest()
	{
		$baseDir = substr($_SERVER['PHP_SELF'], 0, strrpos($_SERVER['PHP_SELF'], '/'));
		$request = substr(urldecode($_SERVER['REQUEST_URI']), strlen($baseDir) + 1);

		if (substr($request, -1) === '/') {
			$request = substr($request, 0, -1);
		}

		return $request;
	}

	protected function parseRequestParams($request)
	{
		$requestParams = explode('/', $request);

		if (isset($requestParams[0]) && $requestParams[0] !== '' && isset($requestParams[1]) && $requestParams[1] !== '') {
			$controller = ucfirst($requestParams[0]) . 'Controller';
			$controller = preg_replace('/[^A-Za-z0-9]/', '', $controller);
			$controller = substr($controller, 0, 255);

			$action = 'action' . ucfirst($requestParams[1]);
			$action = preg_replace('/[^A-Za-z0-9]/', '', $action);
			$action = substr($action, 0, 255);

			$params = array();
			for ($i = 2; $i < count($requestParams); $i += 2) {
				if (isset($requestParams[$i]) && $requestParams[$i] !== '' && isset($requestParams[$i+1])) {
					$params[$requestParams[$i]] = $requestParams[$i+1];
				}
			}
		} else if (isset($requestParams[0]) && $requestParams[0] !== '') {
			$controller = ucfirst($requestParams[0]) . 'Controller';
			$controller = preg_replace('/[^A-Za-z0-9]/', '', $controller);
			$controller = substr($controller, 0, 255);

			$action = $this->defaultAction;
			$params = $this->defaultParams;
		} else {
			$controller = $this->defaultController;
			$action = $this->defaultAction;
			$params = $this->defaultParams;
		}

		return array(
			'controller' => $controller,
			'action' => $action,
			'params' => $params,
		);
	}

	protected function loadController($requestParams)
	{
		$requestedClass = getcwd() . '/../application/controllers/' . $requestParams['controller'] . '.php';

		$errorCode = false;
		$errorMessage = '';

		if (file_exists($requestedClass)) {
			require_once $requestedClass;
			$controller = new $requestParams['controller']($this->container);
			$action = $requestParams['action'];
			$params = $requestParams['params'];

			if (method_exists($controller, $action)) {
				$reflection = new ReflectionMethod($controller, $action);
				$reflectionParams = [];
				$requiredParams = 0;

				foreach ($reflection->getParameters() as $param) {
					$reflectionParams[] = $param->getName();
					if ($param->isOptional() === false) {
						$requiredParams++;
					}
				}

				if (count($requestParams['params']) >= $requiredParams) {
					$i = 0;
					foreach ($requestParams['params'] as $key => $value) {
						if (isset($reflectionParams[$i]) && $reflectionParams[$i] !== $key) {
							$errorCode = 400;
							$errorMessage = 'Wrong parameters given or parameters in wrong order';
							break;
						}
						$i++;
					}
				} else {
					$errorCode = 400;
					$errorMessage = 'Too few parameters given';
				}
			} else {
				$errorCode = 404;
				$errorMessage = 'Page not found';
			}
		} else {
			$errorCode = 404;
			$errorMessage = 'Page not found';
		}

		if ($errorCode !== false) {
			$this->loadErrorController($errorCode, $errorMessage);
		} else {
			call_user_func_array(array($controller, $action), $params);
		}
	}

	protected function loadErrorController($errorCode, $errorMessage)
	{
		$errorClass = getcwd() . '/../application/controllers/' . $this->errorController . '.php';

		if (file_exists($errorClass)) {
			require_once $errorClass;
			$controller = new $this->errorController($container);
			$action = $this->errorAction;
			$params = array($errorCode, $errorMessage);
			call_user_func_array(array($controller, $action), $params);
		} else {
			die('Config error');
		}
	}
}
