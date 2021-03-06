<?php

class Role extends Model
{
	public $id;
	public $name;

	public static function getTableName($prefix = '')
	{
		return $prefix . 'role';
	}

	protected function loadDependencies() {}
	protected function setRules() {}

	public function getByName($name)
	{
		$values = [':name' => $name];
		return $this->getRole($values);
	}

	protected function getRole($values = [])
	{
		$sql = "SELECT id, name FROM " . $this->getTable('Role') . " WHERE 1";
		foreach ($values as $key => $value) {
			$sql .= ' AND ' . substr($key, 1) . ' = ' . $key;
		}
		$this->db->prepare($sql, $values);
		$role = $this->db->query();

		if ($role !== false) {
			$this->id = $role['id'];
			$this->name = $role['name'];

			return true;
		}
		return false;
	}

	public function save() {}
	public function validate() {}
}
