<?php

class Project extends Model
{
	public $id;
	public $projectStatusId;
	public $name;
	public $description;
	public $deleted;

	public $projectStatus;

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
		if (isset($_POST['projectStatusId'])) {
			$this->projectStatusId = Validator::integer($_POST['projectStatusId'], true);
			$post['projectStatusId'] = $this->projectStatusId;
		}
		if (isset($_POST['name'])) {
			$this->name = Validator::string($_POST['name']);
			$post['name'] = $this->name;
		}
		if (isset($_POST['description'])) {
			$this->description = Validator::string($_POST['description']);
			$post['description'] = $this->description;
		}

		return $post;
	}

	public function getById($id)
	{
		if ($id !== '') {
			$values = [':id' => $id];
			return $this->getProject($values);
		}
		return false;
	}

	protected function getProject($values = [])
	{
		$sql = "SELECT p.id, p.project_status_id, p.name, p.description, p.deleted, ps.name AS project_status"
			. " FROM project AS p"
			. " LEFT JOIN project_status AS ps ON ps.id = p.project_status_id"
			. " WHERE p.deleted = 0";

		foreach ($values as $key => $value) {
			$sql .= ' AND u.' . substr($key, 1) . ' = ' . $key;
		}

		$this->db->prepare($sql, $values);
		$project = $this->db->query();

		if ($project !== false) {
			$this->id = $project['id'];
			$this->projectStatusId = $project['project_status_id'];
			$this->name = $project['name'];
			$this->description = $project['description'];
			$this->deleted = $project['deleted'];

			$this->projectStatus = $project['project_status'];

			return true;
		}
		return false;
	}

	public function create($projectStatusId, $name, $description)
	{
		$this->id = 0;
		$this->projectStatusId = $projectStatusId;
		$this->name = $name;
		$this->description = $description;
		$this->deleted = 0;

		if ($this->validate()) {
			$sql = "INSERT INTO project (project_status_id, name, description) VALUES (:project_status_id, :name, :description)";
			$values = [
				':project_status_id' => $this->projectStatusId,
				':name' => $this->name,
				':description' => $this->description,
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
			$sql = "UPDATE project SET id = :id, project_status_id = :project_status_id, name = :name, description = :description, deleted = :deleted WHERE id = :id2";
			$values = [
				':id' => $this->id,
				':project_status_id' => $this->projectStatusId,
				':name' => $this->name,
				':description' => $this->description,
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
			Validator::integer($this->projectStatusId, true),
			Validator::string($this->name),
			Validator::string($this->description),
			Validator::booleanInteger($this->deleted),
		];

		if (in_array(false, $values, true)) {
			return false;
		}
		return true;
	}
}
