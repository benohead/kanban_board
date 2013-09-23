<?php		
		$projects = get_active_projects_with_active_boards();
		if (!isset($projects)) {
			$projects = array();
		}
		$readonly = 0;
		if(!empty($_REQUEST)) {
			if (isset($_REQUEST["cardid"])) {
				$cardid = $_REQUEST["cardid"];
			}
			
			if (isset($_REQUEST["boardid"])) {
				setUsersLastOpenBoard($logged_in_user->user_id, $_REQUEST["boardid"]);
				$current_board = $_REQUEST["boardid"];	
				$current_project = getBoardProjectId($current_board );
			}
			else if (isset($_REQUEST["projectid"])) {
				$current_project = $_REQUEST["projectid"];
				$boards = fetchActiveBoards($current_project);
				if (isset($boards)) {
					$firstBoard = reset($boards);
					if (isset($firstBoard) && isset($firstBoard['id'])) {
						$current_board = $firstBoard['id'];								
					}
				}
			}
			if (isset($_REQUEST["readonly"])) {
				$readonly = $_REQUEST["readonly"];
			}
		}
		if (!isset($current_project) || !isset($current_board)) {
			$current_board = get_usersLastOpenBoard($logged_in_user->user_id);	
			if (isset($current_board)) {
				$current_project = getBoardProjectId($current_board );
			}
			if (!isset($current_project)) {
				$firstProject = reset($projects);
				if (isset($firstProject) && isset($firstProject['id'])) {
					$current_project = $firstProject['id'];
					$boards = fetchActiveBoards($current_project);
					$firstBoard = reset($boards);
					if (isset($firstBoard) && isset($firstBoard['id'])) {
						$current_board = $firstBoard['id'];								
					}
				}
			}
		}
		if (!isset($boards) && isset($current_project)) {
			$boards = fetchActiveBoards($current_project);
		}
		if (!isset($boards)) {
			$boards = array();
		}
?>
