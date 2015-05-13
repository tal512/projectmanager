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
}
