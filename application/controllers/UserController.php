<?php

class UserController extends Controller
{
	protected function loadDependencies() {}

	public function actionLogin()
	{
		if (isset($_POST['email']) && isset($_POST['password'])) {
			$email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
			$password = $_POST['password'];

			$db = $this->container['db'];
			$sql = "SELECT email, password FROM user WHERE email = :email";
			$values = [':email' => $email];
			$db->prepare($sql, $values);
			$user = $db->query();

			if ($user !== false && password_verify($password, $user['password'])) {
				$publicKey = hash('sha256', mt_rand());
				$privateKey = hash('sha256', mt_rand());

				$sql = "UPDATE user SET public_key = :public_key, private_key = :private_key WHERE email = :email AND deleted = 0";
				$values = [
					':public_key' => $publicKey,
					':private_key' => $privateKey,
					':email' => $email
				];
				$db->prepare($sql, $values);

				if ($db->execute()) {
					echo json_encode([
						'status' => 'success',
						'message' => 'User logged in',
						'publicKey' => $publicKey,
						'privateKey' => $privateKey,
					]);
					die;
				}
			}
		}

		echo json_encode([
			'status' => 'error',
			'message' => 'Authentication failed',
		]);
	}

	public function actionRegister()
	{
		$db = $this->container['db'];
		$db->beginTransaction();

		if (isset($_POST['email']) && isset($_POST['password']) && isset($_POST['name'])) {
			$email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
			$password = $_POST['password'];
			$name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

			$user = $this->getUserByEmail($email);

			if ($user === false && $this->registerUser($email, $password, $name)) {
				$userId = $db->lastInsertId();
				if ($this->registerRole($userId, 'member')) {
					echo json_encode([
						'status' => 'success',
						'message' => 'User registered',
					]);
					$db->endTransaction();
					die;
				}
			}
		}

		$db->cancelTransaction();
		echo json_encode([
			'status' => 'error',
			'message' => 'Register failed',
		]);
	}

	private function getUserByEmail($email, $deleted = 0)
	{
		$db = $this->container['db'];
		$sql = "SELECT id FROM user WHERE email = :email AND deleted = :deleted";
		$values = [':email' => $email, ':deleted' => $deleted];
		$db->prepare($sql, $values);
		return $db->query();
	}

	private function registerUser($email, $password, $name)
	{
		$db = $this->container['db'];

		$sql = "INSERT INTO user (email, password, name) VALUES (:email, :password, :name)";
		$values = [
			':email' => $email,
			':password' => password_hash($password, PASSWORD_DEFAULT),
			':name' => $name,
		];
		$db->prepare($sql, $values);

		if ($db->execute()) {
			return true;
		}
		return false;
	}

	private function registerRole($userId, $role)
	{
		$db = $this->container['db'];

		if (is_int($role)) {
			$sql = "INSERT INTO user_role (user_id, role_id) VALUES (:user_id, :role_id)";
			$values = [':user_id' => $userId, ':role_id' => $role];
		} else {
			$sql = "INSERT INTO user_role (user_id, role_id)"
				. " SELECT :user_id, id"
				. " FROM role"
				. " WHERE role.name = :role";
			$values = [':user_id' => $userId, ':role' => $role];
		}

		$db->prepare($sql, $values);
		return $db->execute();
	}

	public function actionUpdate()
	{
		$db = $this->container['db'];
		$db->beginTransaction();

		if (isset($_POST['publicKey']) && isset($_POST['checksum']) && isset($_POST['id'])) {
			$updater = $this->getUserByPublicKey($_POST['publicKey']);
			$update = $this->getUserUpdate();
			$user = $this->getUserById($_POST['id']);

			if ($updater !== false && ($updater['role'] === 'admin' || $updater['id'] === $update['id']) && $user !== false && $this->validateUserUpdate($updater, $update)) {
				$set = [];
				$values = [];
				foreach ($update as $column => $value) {
					if ($column !== 'id' && $column !== 'checksum') {
						$set[] = $column . ' = :' . $column;
						$values[':' . $column] = $value;
					}
					if ($column === 'password') {
						$values[':password'] = password_hash($value, PASSWORD_DEFAULT);
					}
				}
				$sql = "UPDATE user SET " . implode(', ', $set) . ' WHERE id = :id';
				$values[':id'] = $update['id'];
				$db->prepare($sql, $values);

				if ($db->execute()) {
					echo json_encode([
						'status' => 'success',
						'message' => 'User updated',
					]);
					$db->endTransaction();
					die;
				}
			}
		}

		$db->cancelTransaction();
		echo json_encode([
			'status' => 'error',
			'message' => 'Update failed',
		]);
	}

	private function getUserByPublicKey($publicKey, $deleted = 0)
	{
		$db = $this->container['db'];
		$sql = "SELECT u.id, u.public_key, u.private_key, r.name AS role"
			. " FROM user AS u"
			. " JOIN user_role AS ur ON u.id = ur.user_id"
			. " JOIN role AS r ON ur.role_id = r.id"
			. " WHERE u.public_key = :public_key AND u.deleted = :deleted";
		$values = [':public_key' => $publicKey, ':deleted' => $deleted];
		$db->prepare($sql, $values);
		return $db->query();
	}

	private function getUserUpdate()
	{
		$update = [];
		if (isset($_POST['id'])) {
			$update['id'] = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
		}
		if (isset($_POST['email'])) {
			$update['email'] = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
		}
		if (isset($_POST['password'])) {
			$update['password'] = $_POST['password'];
		}
		if (isset($_POST['name'])) {
			$update['name'] = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
		}
		if (isset($_POST['checksum'])) {
			$update['checksum'] = preg_replace('[^A-Za-z0-9]', '', $_POST['checksum']);
		}
		return $update;
	}

	private function getUserById($id, $deleted = 0)
	{
		$db = $this->container['db'];
		$sql = "SELECT id FROM user WHERE id = :id AND deleted = :deleted";
		$values = [':id' => $id, ':deleted' => $deleted];
		$db->prepare($sql, $values);
		return $db->query();
	}

	private function validateUserUpdate($updater, $update)
	{
		$checksum = '';
		if (isset($update['id'])) {
			$checksum .= $update['id'];
		}
		if (isset($update['email'])) {
			$checksum .= $update['email'];
		}
		if (isset($update['password'])) {
			$checksum .= $update['password'];
		}
		if (isset($update['name'])) {
			$checksum .= $update['name'];
		}
		$checksum = hash_hmac('sha256', $checksum, $updater['private_key']);

		if ($checksum === $update['checksum']) {
			return true;
		}
		return false;
	}
}
