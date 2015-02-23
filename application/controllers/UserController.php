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
}
