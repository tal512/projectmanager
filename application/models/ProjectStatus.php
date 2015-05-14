<?php

class ProjectStatus extends Model
{
	public $id;
	public $name;

	public static function getTableName($prefix = '')
	{
		return $prefix . 'project_status';
	}

	protected function loadDependencies()
	{
		parent::loadDependencies();
	}

	protected function setRules()
	{
		$this->rules = [
			'id' => 'integer',
			'name' => 'string',
		];
	}

	public function getById($id)
	{
		if ($id !== '') {
			$values = [':id' => $id];
			return $this->getStatus($values);
		}
		return false;
	}

	protected function getProjectStatus($values = [])
	{
		$sql = "SELECT ps.id, ps.name FROM " . $this->getTable('ProjectStatus') . " AS ps WHERE 1";

		foreach ($values as $key => $value) {
			$sql .= ' AND ps.' . substr($key, 1) . ' = ' . $key;
		}

		$this->db->prepare($sql, $values);
		$status = $this->db->query();

		if ($status !== false) {
			$this->id = $status['id'];
			$this->name = $status['name'];
			return true;
		}
		return false;
	}

	public function validate()
	{
		$values = [
			Validator::integer($this->id),
			Validator::string($this->name),
		];

		if (in_array(false, $values, true)) {
			return false;
		}
		return true;
	}
}
