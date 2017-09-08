<?php
	$results = array();
	$group = '';
	$breakdown = '';
	$valid = true;

	if (isset($_POST['pie_group_drop']) && isset($_POST['pie_breakdown_drop'])) {

		$group = $_POST['pie_group_drop'];
		$breakdown = $_POST['pie_breakdown_drop'];

		switch ($group) { 
			case "league":
				$q_stmt = "SELECT " . $breakdown . " FROM PLAYER";
				break;
			case "team":
				$team = $_POST['pie_team_drop'];
				$q_stmt = "SELECT " . $breakdown . " FROM PLAYER WHERE current_team = '" . $team . "'";
				break;
			case "filter":
				$graph_filter = true;
				include '../Filter/filter.php';
				$q_stmt = "SELECT " . $breakdown . " FROM PLAYER WHERE player_id = :id ";
				break;
		}

		if ($group === "filter") {
			$filtered_playerIDs = array_unique($filtered_playerIDs); // ensure players aren't repeated

			foreach ($filtered_playerIDs as $id) {
				$query = $db->prepare($q_stmt);
				$query->bindValue(":id", $id);
				$result = $query->execute();

				while ($row = $result->fetchArray()) {
					if ($row[$breakdown] === null) {
						continue;
					}

					if (array_key_exists($row[$breakdown], $results)) {
						$results[$row[$breakdown]] += 1;
					}
					else {
						$results[$row[$breakdown]] = 1;
					}
				}
			}
		}
		else {
			$query = $db->prepare($q_stmt);
			$result = $query->execute();

			while ($row = $result->fetchArray()) {
				if ($row[$breakdown] === null) {
					continue;
				}

				if (array_key_exists($row[$breakdown], $results)) {
					$results[$row[$breakdown]] += 1;
				}
				else {
					$results[$row[$breakdown]] = 1;
				}
			}
		}

		if ($breakdown === 'weight' && $group === "league") {
			$min = floatval(min(array_keys($results)));
			$max = floatval(max(array_keys($results)));
			$new_results = array();
			$last_value = 0;

			$increment = 20;

			for ($i = $min; $i < $max; $i += $increment) {
				$new_results[strval($i) . "-" . strval($i + ($increment - 1))] = 0;
				$last_value = $i;
			}

			if ($last_value + $increment <= $max) {
				$new_results[strval($last_value + ($increment - 1)) . "-" . strval($max)] = 0;
			}

			foreach ($results as $k=>$v) {
				foreach ($new_results as $g=>$w) {
					echo $v . "<br>";
					echo floatval(explode('-', $g)[0]) . "<br>";
					echo floatval(explode('-', $g)[1]) . "<br>";
					if ($k >= floatval(explode('-', $g)[0]) && $k <= floatval(explode('-', $g)[1])) {
						$new_results[$g] += $v;
						break;
					}
				}
			}

			$results = $new_results;
		}

		if ($breakdown === "college" && $group === "league") {
			$new_results = array("Other (1 or 2 players per)"=>0);

			foreach ($results as $k=>$v) {
				if ($v <= 2) {
					$new_results["Other (1 or 2 players per)"] += 1;
				}
				else {
					$new_results[$k] = $v;
				}
			}

			$results = $new_results;
		}

		if ($breakdown === 'salary') { // use ranges because salaries are so varied
			$new_results = array();

			$min = 500000;
			$max = floatval(max(array_keys($results)));
			$last_value = 0;

			$increment = 3500000;

			for ($i = $min; $i < $max; $i += $increment) {
				$new_results[convert_salary(strval($i)) . "-" . convert_salary(strval($i + ($increment - 1)))] = 0;
				$last_value = $i;
			}

			if ($last_value + $increment <= $max) {
				$new_results[convert_salary(strval($last_value + ($increment - 1))) . "-" . convert_salary(strval($max))] = 0;
			}

			foreach ($results as $k=>$v) {
				foreach ($new_results as $g=>$w) {
					// convert the values in the range from salaries to floats and check if the key is in that range
					if ($k >= floatval(convert_salary_back(explode('-', $g)[0])) && $k <= floatval(convert_salary_back(explode('-', $g)[1]))) {
						$new_results[$g] += $v;
						break;
					}
				}
			}

			$results = $new_results;
		}
	}
	else {
		$valid = false;
	}
?>