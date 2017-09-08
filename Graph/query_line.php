<?php
	$results = array();
	$names = array();
	$x_label = '';
	$y_label = '';
	$st = '';

	if (isset($_POST['line_x_axis_drop']) && isset($_POST['line_y_axis_drop']) && isset($_POST['line_season_type_radio'])) {

		$x_label = $_POST['line_x_axis_drop'];
		$y_label = $_POST['line_y_axis_drop'];
		$st = $_POST['line_season_type_radio'];

		for ($i = 1; $i <= 10; $i++) {
			$form = 'extra_name_' . strval($i);
			if (isset($_POST[$form]) && trim($_POST[$form]) !== "") {
				array_push($names, trim($_POST[$form]));
			}
		}

		foreach ($names as $name) {
			$q_stmt = 'SELECT ' . $x_label . ', ' . $y_label .
				' FROM SEASON_STATS WHERE 
				name = "' . $name . '" AND season_type = "' . $st . '"';
			$query = $db->prepare($q_stmt);
			$result = $query->execute();
			while ($row = $result->fetchArray()) {
				$results[$name][$row[$x_label]] = $row[$y_label];
			}
		}
	}
?>