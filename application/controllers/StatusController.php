<?php

class StatusController extends Controller
{
	protected function loadDependencies()
	{
		parent::loadDependencies();
		require_once $this->config['app']['dir'] . '/models/ProjectStatus.php';
	}

	public function getAll()
	{
		$sql = "SELECT id, name FROM " . $this->getTable('ProjectStatus');
		$this->db->prepare($sql);
		$statuses = $this->db->queryAll();
		
		$list = [];
		foreach($statuses as $status) {
			$result[$status->id] = new Status($this->container);
			$result[$status->id]->id = $status->id;
			$result[$status->id]->name = $status->name;
		}
		return $list;
	}
}
