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

	protected function loadDependencies() {}

	public function getByEmail($email)
	{
		$values = [':email' => $email];
		return $this->getUser($values);
	}

	protected function getUser($values = [])
	{
		$sql = "SELECT id, email, password, name, public_key, private_key, deleted FROM user WHERE deleted = 0";
		foreach ($values as $key => $value) {
			$sql .= ' AND ' . substr($key, 1) . ' = ' . $key;
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

			return true;
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
		$password = password_get_info($this->password);
		$values = [
			filter_var($this->id, FILTER_VALIDATE_REGEXP, ['options' => ['regexp' => '/^[0-9]+$/']]),
			filter_var($this->email, FILTER_VALIDATE_EMAIL),
			($password['algo'] !== 0) ? true : false,
			filter_var($this->name, FILTER_SANITIZE_FULL_SPECIAL_CHARS),
			filter_var($this->publicKey, FILTER_VALIDATE_REGEXP, ['options' => ['regexp' => '/^[a-f0-9]*$/']]),
			filter_var($this->privateKey, FILTER_VALIDATE_REGEXP, ['options' => ['regexp' => '/^[a-f0-9]*$/']]),
			filter_var($this->deleted, FILTER_VALIDATE_REGEXP, ['options' => ['regexp' => '/^[0-9]{1}$/']]),
		];

		if (in_array(false, $values, true)) {
			return false;
		}
		return true;
	}
}