<?php

class User extends Model
{
	public $role;
	public $checksum;

	public static function getTableName($prefix = '')
	{
		return $prefix . 'user';
	}

	protected function loadDependencies()
	{
		parent::loadDependencies();
	}

	protected function setRules()
	{
		$this->rules = [
			'id' => 'integer',
			'email' => 'email',
			'password' => 'safe',
			'name' => 'string',
			'authKey' => 'hexadecimal',
			'deleted' => 'booleanInteger',
		];
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

	public function getByAuthKey($authKey)
	{
		if ($authKey !== '') {
			$values = [':authKey' => $authKey];
			return $this->getUser($values);
		}
		return false;
	}

	protected function getUser($values = [])
	{
		$sql = "SELECT u.id, u.email, u.password, u.name, u.auth_key, u.deleted, r.name AS role"
			. " FROM " . $this->getTable('User') . " AS u"
			. " LEFT JOIN " . $this->getTable('User') . "_role AS ur ON ur.user_id = u.id AND ur.deleted = 0"
			. " LEFT JOIN " . $this->getTable('Role') . " AS r ON r.id = ur.role_id"
			. " WHERE u.deleted = 0";

		foreach ($values as $key => $value) {
			$sql .= ' AND u.' . $this->camelCaseToUnderscore(substr($key, 1)) . ' = ' . $key;
		}

		$this->db->prepare($sql, $values);
		$user = $this->db->query();

		if ($user !== false) {
			$this->id = $user['id'];
			$this->email = $user['email'];
			$this->password = $user['password'];
			$this->name = $user['name'];
			$this->authKey = $user['auth_key'];
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
		$this->authKey = '';
		$this->deleted = 0;

		if ($this->validate()) {
			$sql = "INSERT INTO " . $this->getTable('User') . " (email, password, name) VALUES (:email, :password, :name)";
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

	public function assignRole($roleId)
	{
		$sql = "UPDATE " . $this->getTable('User') . "_role SET deleted = 1 WHERE user_id = :user_id";
		$values = [':user_id' => $this->id];
		$this->db->prepare($sql, $values);

		if ($this->db->execute()) {
			$sql = "INSERT INTO " . $this->getTable('User') . "_role (user_id, role_id) VALUES (:user_id, :role_id)";
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
