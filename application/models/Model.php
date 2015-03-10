<?php

interface TableNameInterface {
	/**
	* getTableName is not recommended way to get table name for model, please see
	* BaseClass getTable -function
	*/
	public static function getTableName();
}


abstract class Model extends BaseClass implements TableNameInterface
{
	protected $attributes;
	protected $rules;

	abstract protected function setRules();

	public function __construct(&$container) {
		parent::__construct($container);
		$this->setRules();
	}

	protected function loadDependencies() {
		require_once $this->config['app']['dir'] . '/models/Validator.php';
	}

	public function loadPost()
	{
		$post = [];
		foreach (array_keys($this->rules) as $attribute) {
			if (isset($_POST[$attribute])) {
				$value = $_POST[$attribute];
				$validator = $this->rules[$attribute];
				$this->$attribute = Validator::$validator($value);
				$post[$attribute] = $this->$attribute;
			}
		}
		return $post;
	}

	public function validate()
	{
		foreach ($this->attributes as $name => $value) {
			$validator = $this->rules[$name];
			if (Validator::$validator($value) === false) {
				return false;
			}
		}
		return true;
	}

	public function save()
	{
		if ($this->validate()) {
			$sql = "UPDATE " . $this->getTableName() . " SET ";
			$set = [];
			$values = [':id2' => $this->id];
			foreach ($this->attributes as $name => $value) {
				$underscoredName = $this->camelCaseToUnderscore($name);
				$set[] = "{$underscoredName} = :{$underscoredName}";
				$values[":{$underscoredName}"] = $value;
			}
			$sql .= implode(', ', $set);
			$sql .= " WHERE id = :id2";
			$this->db->prepare($sql, $values);
			return $this->db->execute();
		}
		return false;
	}

	public function __get($name)
	{
		return $this->attributes[$name];
	}

	public function __set($name, $value)
	{
		$this->attributes[$name] = $value;
	}
}
