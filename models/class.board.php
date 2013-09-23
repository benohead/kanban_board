<?php
class Board {
	public $status = false;
	public $active;
	public $projectid;
	public $boardname;
	public $displayname;
	public $sql_failure = false;
	public $boardname_taken = false;
	public $displayname_taken = false;
	public $success = NULL;

	function __construct($projectid, $board, $display, $active, $templateid, $attributesid, $cloneboardid, $copycards) {
		$this->active = $active;
		$this->projectid = $projectid;
		$this->templateid = $templateid;
		$this->attributesid = $attributesid;
		$this->cloneboardid = $cloneboardid;
		$this->copycards = $copycards;

		//Used for display only
		$this->displayname = $display;

		//Sanitize
		$this->boardname = sanitize($board);

		if ($this->boardNameExists()) {
			$this->boardname_taken = true;
		} else
			if ($this->displayname_exists()) {
			$this->displayname_taken = true;
		} else {
			//No problems have been found.
			$this->status = true;
		}
	}

	public function addBoard() {
		global $mysqli, $db_table_prefix;
//Prevent this function being called if there were construction errors
		if ($this->status) {
			if ($this->cloneboardid == 0) {
				//Insert the board into the database providing no errors have been found.
				$stmt = $mysqli->prepare("INSERT INTO " . $db_table_prefix . "boards (project_id, board_name, display_name, active, board_css, board_js, card_attributes, card_css, card_js ) SELECT ?, ?, ?, ?, board_css, board_js, card_attributes, card_css, card_js FROM " . $db_table_prefix . "board_templates INNER JOIN " . $db_table_prefix . "card_templates WHERE " . $db_table_prefix . "board_templates.id = ? AND " . $db_table_prefix . "card_templates.id = ? ");
				$stmt->bind_param("issiii", $this->projectid, $this->boardname, $this->displayname, $this->active, $this->templateid, $this->attributesid);
				$stmt->execute();
				$inserted_id = $mysqli->insert_id;
				$stmt->close();
				$stmt = $mysqli->prepare("INSERT INTO " . $db_table_prefix . "board_columns (board_id, column_name, display_name, wip_limit, order_nr, description) SELECT ?, column_name, display_name, wip_limit, order_nr, description FROM " . $db_table_prefix . "board_template_columns WHERE " . $db_table_prefix . "board_template_columns.template_id = ? ");
				$stmt->bind_param("ii", $inserted_id, $this->templateid);
				$stmt->execute();
				$inserted_id = $mysqli->insert_id;
				$stmt->close();
			}
			else {
				$stmtString = "INSERT INTO " . $db_table_prefix . "boards ( project_id, board_name, display_name, active, board_css, board_js, card_attributes, card_css, card_js";
				if ($this->copycards != 0) {
					$stmtString = $stmtString . ",cards";
				}
				$stmtString = $stmtString . ") SELECT ?, ?, ?, ?, board_css, board_js, card_attributes, card_css, card_js";
				if ($this->copycards != 0) {
					$stmtString = $stmtString . ",cards";
				}
				$stmtString = $stmtString . " FROM " . $db_table_prefix . "boards
						WHERE " . $db_table_prefix . "boards.id = ?";
				$stmt = $mysqli->prepare($stmtString);
				$stmt->bind_param("issii", $this->projectid, $this->boardname, $this->displayname, $this->active, $this->cloneboardid);
				$stmt->execute();
				$inserted_id = $mysqli->insert_id;
				$stmt->close();
				$stmt = $mysqli->prepare("INSERT INTO " . $db_table_prefix . "board_columns (board_id, column_name, display_name, wip_limit, order_nr, description) SELECT ?, column_name, display_name, wip_limit, order_nr, description FROM " . $db_table_prefix . "board_columns WHERE " . $db_table_prefix . "board_columns.board_id = ? ");
				$stmt->bind_param("ii", $inserted_id, $this->cloneboardid);
				$stmt->execute();
				$inserted_id = $mysqli->insert_id;
				$stmt->close();
			}
			return ($inserted_id > 0);
		}
		return false;
	}

	//Checks if a board name exists in the DB
	function boardNameExists() {
		global $mysqli, $db_table_prefix;
		$stmt = $mysqli->prepare("SELECT active
				FROM " . $db_table_prefix . "boards
				WHERE
				board_name = ?
				LIMIT 1");
		$stmt->bind_param("s", $this->boardname);
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
				FROM " . $db_table_prefix . "boards
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