<?php

class ProjectController extends Controller
{
	protected function loadDependencies()
	{
		parent::loadDependencies();
		require_once $this->config['app']['dir'] . '/models/Project.php';
	}

	public function actionCreate()
	{
		$this->db->beginTransaction();

		if (isset($_POST['name']) && isset($_POST['description'])) {
			$name = Validator::string($_POST['name']);
			$description = Validator::string($_POST['description']);
			$status = 1;
			
			$project = new Project($this->container);

			if ($project->create($status, $name, $description)) {
				$this->db->endTransaction();
				$this->renderJson([
					'status' => 'success',
					'message' => 'Project created registered',
				]);
			}
		}

		$this->db->cancelTransaction();
		$this->renderJson([
			'status' => 'error',
			'message' => 'Create project failed',
		]);
	}
	
	public function getAll()
	{
		$sql = "SELECT p.id, p.project_status_id, p.name, p.description, p.deleted, ps.name AS project_status"
			. " FROM " . $this->getTable('Project') . " AS p"
			. " LEFT JOIN " . $this->getTable('Project') . "_status AS ps ON ps.id = p.project_status_id"
			. " WHERE p.deleted = 0";
		$this->db->prepare($sql);
		$projects = $this->db->queryAll();

		if ($projects !== false) {
			$list = [];
			foreach($projects as $project) {
				$list[$project->id] = new Project($this->container);
				$list[$project->id]->id = $project['id'];
				$list[$project->id]->projectStatusId = $project['project_status_id'];
				$list[$project->id]->name = $project['name'];
				$list[$project->id]->description = $project['description'];
				$list[$project->id]->deleted = $project['deleted'];
				$list[$project->id]->projectStatus = $project['project_status'];
			}
			return $list;
		}
		return false;
	}
}
