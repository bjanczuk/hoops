<?php
	$results = array();
	$players = array();
	$names = array();
	$stats = array();
	$player = '';
	$x_label = '';
	$x_axis = '';
	$y_label = '';
	$st = '';
	$type = '';

	if (isset($_POST['bar_x_axis_drop']) && isset($_POST['bar_season_type_radio']) && isset($_POST['extra_name_1']) && isset($_POST['bar_y_axis_drop_1'])) {

		if (isset($_POST['bar_y_axis_drop_2'])) {
			$type = 'player';
		}
		else {
			$type = 'stat';
		}

		$x_axis = $_POST['bar_x_axis_drop'];
		$st = $_POST['bar_season_type_radio'];
		array_push($players, $_POST['extra_name_1']);
		array_push($stats, $_POST['bar_y_axis_drop_1']);

		for ($i = 2; $i <= 10; $i++) {
			$form = 'extra_name_' . strval($i);
			if (isset($_POST[$form]) && trim($_POST[$form]) !== "") {
				array_push($players, trim($_POST[$form]));
			}
		}
		$stats = array_unique($stats);

		for ($i = 2; $i <= 5; $i++) {
			$form = 'bar_y_axis_drop_' . strval($i);
			if (isset($_POST[$form]) && $_POST[$form] !== "_") {
				array_push($stats, $_POST[$form]);
			}
		}
		$players = array_unique($players);

		$q_stmt = "SELECT " . $x_axis . ", ";
		foreach ($stats as $stat) {
			$q_stmt = $q_stmt . $stat . ", ";
		}

		$q_stmt = trim($q_stmt);
		$q_stmt = substr($q_stmt, 0, strlen($q_stmt) - 1) . " FROM SEASON_STATS WHERE season_type = '" . $st .  "' AND name = :name";
		
		foreach ($players as $player) {
			$player = str_replace('_', ' ', $player);

			$query = $db->prepare($q_stmt);
			$query->bindValue(':name', $player);
			$result = $query->execute();

			while ($row = $result->fetchArray()) {
				$season = $row[$x_axis];
				foreach ($row as $k=>$v) {
					if ($k !== $x_axis) {
						$results[$player][$season][$k] = $v;
					}
				}
			}
		}
	}
?>