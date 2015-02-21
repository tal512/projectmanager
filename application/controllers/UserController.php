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
				$publicKey = sha1($email . rand() . time());
				$privateKey = sha1($email . rand() . time());

				$sql = "UPDATE user SET public_key = :public_key AND private_key = :private_key WHERE email = :email";
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
		if (isset($_POST['email']) && isset($_POST['password']) && isset($_POST['name'])) {
			$email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
			$password = $_POST['password'];
			$name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

			$db = $this->container['db'];
			$sql = "SELECT id FROM user WHERE email = :email AND deleted = 0";
			$values = [':email' => $email];
			$db->prepare($sql, $values);
			$user = $db->query();

			if ($user === false) {
				$sql = "INSERT INTO user (email, password, name) VALUES (:email, :password, :name)";
				$values = [
					':email' => $email,
					':password' => password_hash($password, PASSWORD_DEFAULT),
					':name' => $name,
				];
				$db->prepare($sql, $values);

				if ($db->execute()) {
					echo json_encode([
						'status' => 'success',
						'message' => 'User registered',
					]);
				}
				die;
			}
		}

		echo json_encode([
			'status' => 'error',
			'message' => 'Register failed',
		]);
	}
}
