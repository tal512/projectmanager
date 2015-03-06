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
				$user->publicKey = hash('sha256', mt_rand());
				$user->privateKey = hash('sha256', mt_rand());

				if ($user->save()) {
					$this->renderJson([
						'status' => 'success',
						'message' => 'User logged in',
						'publicKey' => $user->publicKey,
						'privateKey' => $user->privateKey,
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
		if (isset($_POST['publicKey'])) {
			$publicKey = Validator::hexadecimal($_POST['publicKey']);

			$user = new User($this->container);

			if ($user->getByPublicKey($publicKey) !== false) {
				$user->publicKey = '';
				$user->privateKey = '';

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

		if (isset($_POST['publicKey']) && isset($_POST['checksum']) && isset($_POST['id'])) {
			$publicKey = Validator::hexadecimal($_POST['publicKey']);
			$id = Validator::integer($_POST['id']);

			$updater = new User($this->container);
			$updatee = new User($this->container);

			if ($updater->getByPublicKey($publicKey) !== false && ($updater->role === 'admin' || $updater->id === $id) && $updatee->getById($id) !== false && $this->validateUserUpdate($updater, $updatee) !== false) {
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

	private function validateUserUpdate($updater, $updatee)
	{
		$post = $updatee->loadPost();

		$checksum = '';
		if (isset($post['id'])) {
			$checksum .= $post['id'];
		}
		if (isset($post['email'])) {
			$checksum .= $post['email'];
		}
		if (isset($post['password'])) {
			$checksum .= $post['password'];
		}
		if (isset($post['name'])) {
			$checksum .= $post['name'];
		}
		$checksum = hash_hmac('sha256', $checksum, $updater->privateKey);

		if ($checksum === $post['checksum']) {
			return true;
		}
		return false;
	}
}
