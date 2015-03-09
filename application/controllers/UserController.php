<?php

class UserController extends Controller
{
	protected function loadDependencies()
	{
		parent::loadDependencies();
		require_once $this->config['app']['dir'] . '/models/Role.php';
		require_once $this->config['app']['dir'] . '/models/User.php';
	}

	public function actionLogin()
	{
		if (isset($_POST['email']) && isset($_POST['password'])) {
			$email = Validator::email($_POST['email']);
			$password = $_POST['password'];

			$user = new User($this->container);

			if ($user->getByEmail($email) !== false && password_verify($password, $user->password)) {
				$user->authKey = hash('sha256', mt_rand());

				if ($user->save()) {
					$this->renderJson([
						'status' => 'success',
						'message' => 'User logged in',
						'authKey' => $user->authKey,
					]);
				}
			}
		}

		$this->renderJson([
			'status' => 'error',
			'message' => 'Authentication failed',
		]);
	}

	public function actionLogout()
	{
		if (isset($_POST['authKey'])) {
			$authKey = Validator::hexadecimal($_POST['authKey']);

			$user = new User($this->container);

			if ($user->getByAuthKey($authKey) !== false) {
				$user->authKey = '';

				if ($user->save()) {
					$this->renderJson([
						'status' => 'success',
						'message' => 'User logged out',
					]);
				}
			}
		}

		$this->renderJson([
			'status' => 'error',
			'message' => 'Logout failed',
		]);
	}

	public function actionRegister()
	{
		$this->db->beginTransaction();

		if (isset($_POST['email']) && isset($_POST['password']) && isset($_POST['name'])) {
			$email = Validator::email($_POST['email']);
			$password = $_POST['password'];
			$name = Validator::string($_POST['email']);

			$user = new User($this->container);
			$role = new Role($this->container);

			if ($user->getByEmail($email) === false && $role->getByName('member') !== false && $user->create($email, $password, $name) && $user->assignRole($role->id)) {
				$this->db->endTransaction();
				$this->renderJson([
					'status' => 'success',
					'message' => 'User registered',
				]);
			}
		}

		$this->db->cancelTransaction();
		$this->renderJson([
			'status' => 'error',
			'message' => 'Register failed',
		]);
	}

	public function actionUpdate()
	{
		$this->db->beginTransaction();

		if (isset($_POST['authKey']) && isset($_POST['id'])) {
			$authKey = Validator::hexadecimal($_POST['authKey']);
			$id = Validator::integer($_POST['id']);

			$updater = new User($this->container);
			$updatee = new User($this->container);

			if ($updater->getByAuthKey($authKey) !== false && ($updater->role === 'admin' || $updater->id === $id) && $updatee->getById($id) !== false) {
				$updatee->loadPost();
				$updatee->password = password_hash($updatee->password, PASSWORD_DEFAULT);
				if ($updatee->save()) {
					$this->db->endTransaction();
					$this->renderJson([
						'status' => 'success',
						'message' => 'User updated',
					]);
				}
			}
		}

		$this->db->cancelTransaction();
		$this->renderJson([
			'status' => 'error',
			'message' => 'Update failed',
		]);
	}
}
