<?php

class User extends Model
{
	public $id;
	public $email;
	public $password;
	public $name;
	public $publicKey;
	public $privateKey;
	public $deleted;

	public $role;
	public $checksum;

	protected function loadDependencies()
	{
		parent::loadDependencies();
	}

	public function loadPost()
	{
		$post = [];

		if (isset($_POST['id'])) {
			$this->id = Validator::integer($_POST['id']);
			$post['id'] = $this->id;
		}
		if (isset($_POST['email'])) {
			$this->email = Validator::email($_POST['email']);
			$post['email'] = $this->email;
		}
		if (isset($_POST['password'])) {
			$this->password = $_POST['password'];
			$post['password'] = $this->password;
		}
		if (isset($_POST['name'])) {
			$this->name = Validator::string($_POST['name']);
			$post['name'] = $this->name;
		}
		if (isset($_POST['checksum'])) {
			$this->checksum = $_POST['checksum'];
			$post['checksum'] = $this->checksum;
		}

		return $post;
	}

	public function getByEmail($email)
	{
		if ($email !== '') {
			$values = [':email' => $email];
			return $this->getUser($values);
		}
		return false;
	}

	public function getById($id)
	{
		if ($id !== '') {
			$values = [':id' => $id];
			return $this->getUser($values);
		}
		return false;
	}

	public function getByPublicKey($publicKey)
	{
		if ($publicKey !== '') {
			$values = [':public_key' => $publicKey];
			return $this->getUser($values);
		}
		return false;
	}

	protected function getUser($values = [])
	{
		$sql = "SELECT u.id, u.email, u.password, u.name, u.public_key, u.private_key, u.deleted, r.name AS role"
			. " FROM user AS u"
			. " LEFT JOIN user_role AS ur ON ur.user_id = u.id AND ur.deleted = 0"
			. " LEFT JOIN role AS r ON r.id = ur.role_id"
			. " WHERE u.deleted = 0";

		foreach ($values as $key => $value) {
			$sql .= ' AND u.' . substr($key, 1) . ' = ' . $key;
		}

		$this->db->prepare($sql, $values);
		$user = $this->db->query();

		if ($user !== false) {
			$this->id = $user['id'];
			$this->email = $user['email'];
			$this->password = $user['password'];
			$this->name = $user['name'];
			$this->publicKey = $user['public_key'];
			$this->privateKey = $user['private_key'];
			$this->deleted = $user['deleted'];

			$this->role = $user['role'];

			return true;
		}
		return false;
	}

	public function create($email, $password, $name)
	{
		$this->id = 0;
		$this->email = $email;
		$this->password = password_hash($password, PASSWORD_DEFAULT);
		$this->name = $name;
		$this->publicKey = '';
		$this->privateKey = '';
		$this->deleted = 0;

		if ($this->validate()) {
			$sql = "INSERT INTO user (email, password, name) VALUES (:email, :password, :name)";
			$values = [
				':email' => $this->email,
				':password' => $this->password,
				':name' => $this->name,
			];
			$this->db->prepare($sql, $values);

			if ($this->db->execute()) {
				$this->id = $this->db->lastInsertId();
				return true;
			}
		}

		return false;
	}

	public function save()
	{
		if ($this->validate()) {
			$sql = "UPDATE user SET id = :id, email = :email, password = :password, name = :name, public_key = :public_key, private_key = :private_key, deleted = :deleted WHERE id = :id2";
			$values = [
				':id' => $this->id,
				':email' => $this->email,
				':password' => $this->password,
				':name' => $this->name,
				':public_key' => $this->publicKey,
				':private_key' => $this->privateKey,
				':deleted' => $this->deleted,
				':id2' => $this->id,
			];
			$this->db->prepare($sql, $values);
			return $this->db->execute();
		}
		return false;
	}

	public function validate()
	{
		$values = [
			Validator::integer($this->id),
			Validator::email($this->email),
			Validator::password($this->password),
			Validator::string($this->name),
			Validator::hexadecimal($this->publicKey),
			Validator::hexadecimal($this->privateKey),
			Validator::booleanInteger($this->deleted),
		];

		if (in_array(false, $values, true)) {
			return false;
		}
		return true;
	}

	public function assignRole($roleId)
	{
		$sql = "UPDATE user_role SET deleted = 1 WHERE user_id = :user_id";
		$values = [':user_id' => $this->id];
		$this->db->prepare($sql, $values);

		if ($this->db->execute()) {
			$sql = "INSERT INTO user_role (user_id, role_id) VALUES (:user_id, :role_id)";
			$values = [
				':user_id' => $this->id,
				':role_id' => $roleId,
			];
			$this->db->prepare($sql, $values);
			return $this->db->execute();
		}

		return false;
	}
}
