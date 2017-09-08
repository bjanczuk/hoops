<?php
	$results = array();
	$names = array();
	$x_label = '';
	$y_label = '';
	$st = '';

	if (isset($_POST['scatter_x_axis_drop']) && isset($_POST['scatter_y_axis_drop']) && isset($_POST['scatter_season_type_radio'])) {
		$x_label = $_POST['scatter_x_axis_drop'];

		$y_label = $_POST['scatter_y_axis_drop'];

		$st = $_POST['scatter_season_type_radio'];

		for ($i = 1; $i <= 10; $i++) {
			$form = 'extra_name_' . strval($i);
			if (isset($_POST[$form]) && trim($_POST[$form]) !== "") {
				array_push($names, trim($_POST[$form]));
			}
		}

		foreach ($names as $name) {
			$results[$name]['season'] = array();

			$q_stmt = 'SELECT season, ' . $x_label . ', ' . $y_label .
				' FROM SEASON_STATS WHERE 
				name = "' . $name . '" AND season_type = "' . $st . '"';
			$query = $db->prepare($q_stmt);
			$result = $query->execute();
			while ($row = $result->fetchArray()) {
				array_push($results[$name], array(strval($row[$x_label]), $row[$y_label]));
				array_push($results[$name]['season'], $row['season']);
			}
		}
	}
?>