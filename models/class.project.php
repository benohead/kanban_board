<?php
class Project {
	public $status = false;
	private $active;
	private $projectname;
	private $displayname;
	public $sql_failure = false;
	public $projectname_taken = false;
	public $displayname_taken = false;
	public $success = NULL;

	function __construct($project, $display, $active) {
		$this->active = $active;

		//Used for display only
		$this->displayname = $display;

		//Sanitize
		$this->projectname = sanitize($project);

		if ($this->projectNameExists()) {
			$this->projectname_taken = true;
		} else
			if ($this->displayname_exists()) {
				$this->displayname_taken = true;
			} else {
				//No problems have been found.
				$this->status = true;
			}
	}

	public function addProject() {
		global $mysqli, $db_table_prefix;

		//Prevent this function being called if there were construction errors
		if ($this->status) {
			//Insert the project into the database providing no errors have been found.
			$stmt = $mysqli->prepare("INSERT INTO " . $db_table_prefix . "projects (
								project_name,
								display_name,
								active
						)
								VALUES (
								?,
								?,
								?
						)");

			$stmt->bind_param("ssi", $this->projectname, $this->displayname, $this->active);
			$stmt->execute();
			$inserted_id = $mysqli->insert_id;
			$stmt->close();
			return ($inserted_id > 0);
		} else {
			return false;
		}
	}

	//Checks if a project name exists in the DB
	function projectNameExists() {
		global $mysqli, $db_table_prefix;
		$stmt = $mysqli->prepare("SELECT active
						FROM " . $db_table_prefix . "projects
						WHERE
						project_name = ?
						LIMIT 1");
		$stmt->bind_param("s", $this->projectname);
		$stmt->execute();
		$stmt->store_result();
		$num_returns = $stmt->num_rows;
		$stmt->close();

		if ($num_returns > 0) {
			return true;
		} else {
			return false;
		}
	}

	//Checks if a display name exists in the DB
	function displayname_exists() {
		global $mysqli, $db_table_prefix;
		$stmt = $mysqli->prepare("SELECT active
						FROM " . $db_table_prefix . "projects
						WHERE
						display_name = ?
						LIMIT 1");
		$stmt->bind_param("s", $this->displayname);
		$stmt->execute();
		$stmt->store_result();
		$num_returns = $stmt->num_rows;
		$stmt->close();

		if ($num_returns > 0) {
			return true;
		} else {
			return false;
		}
	}
}
?>