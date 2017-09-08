<script>
	$("body").css("overflow-x", "hidden");

	function submitForm(value) {
		var k = value.split('__')[0];
		var v = value.split('__')[1];
		var newElement;
		var form  = document.getElementById("hidden_filter");

		if (["weight", "salary", "draft_pos", "draft_year"].includes(k)) {
			sessionStorage.setItem('tableHeaderClick', k);
		}
		else {
			sessionStorage.setItem("tableHeaderClick", null);
		}

		if (k == "position") {
			checkbox = document.createElement("input");
			checkbox.type = "checkbox";
			checkbox.name = v + "_check";
			checkbox.value = checkbox.name;
			checkbox.checked = "true";
			form.append(checkbox);
		}

		else if (k == "weight" || k == "age" || k == "salary" ||
			k == "draft_pos") {
			drop = document.createElement("select");
			drop.name = k + "_drop";

			option = document.createElement("option");
			if (k == "weight") {
				if (v < 220) { option.value = "at_most"; }
				else { option.value = "at_least"; }
			}
			if (k == "age" || k == "draft_pos") {
				option.value = "exactly";
			}
			if (k == "salary") {
				if (v < 5500000) { option.value = "at_most"; }
				else { option.value = "at_least"; }
			}
			drop.append(option);

			spec = document.createElement("input");
			spec.type = "text";
			spec.name = k + "_spec";
			spec.value = v;

			form.append(drop);
			form.append(spec);
		}

		else if (k == "height" || k == "experience" || k == "draft_year") {
			drop = document.createElement("select");
			drop.name = k + "_drop";

			option = document.createElement("option");
			if (k == "draft_year") { option.value = "in"; }
			else { option.value = "exactly"; }
			drop.append(option);

			spec = document.createElement("select");
			spec.name = k + "_spec";

			option2 = document.createElement("option");
			option2.value = v;
			spec.append(option2);

			form.append(drop);
			form.append(spec);
		}

		else if (k == "college") {
			drop = document.createElement("select");
			drop.name = k + "_drop";

			option = document.createElement("option");
			option.value = v;
			drop.append(option);

			form.append(drop);
		}

		// Add player draft years as a column if draft position was clicked on
		if (k == "draft_pos") {
			drop = document.createElement("select");
			drop.name = "draft_year_drop";

			option = document.createElement("option");
			option.value = "after";
			drop.append(option);

			spec = document.createElement("select");
			spec.name = "draft_year_spec";

			option2 = document.createElement("option");
			option2.value = "0";
			spec.append(option2);

			form.append(drop);
			form.append(spec);
		}

		// Add player draft positions if draft year was clicked on
		if (k == "draft_year") {
			drop = document.createElement("select");
			drop.name = "draft_pos_drop";

			option = document.createElement("option");
			option.value = "at_least";
			drop.append(option);

			spec = document.createElement("input");
			spec.type = "text";
			spec.name = "draft_pos_spec";
			spec.value = "0";

			form.append(drop);
			form.append(spec);
		}

		form.submit();
	}
</script>

<?php
	$basics = array('current_team'=>'Team', 'position'=>'Position',
					'jersey_num'=>'Jersey #',
					'height'=>'Height', 'weight'=>'Weight',
					'age'=>'Age', 'experience'=>'Experience',
					'college'=>'College', 'draft_pos'=>'Draft Position',
					'draft_year'=>"Draft Year", 'salary'=>'2017-18 Salary');

	$row = $result->fetchArray();
	echo '<div id="' . convertNameToURL($row['current_team']) . '_div">';

	$name = $row['name'];
	echo "<div class='name_header'><h1 id='name_header' class='text_team_color'>" . $name . "</h1></div>";

	$team_class = convertNameToURL($row['current_team']) . '_player';

	if (file_exists("../Images/Players/Headshots/" . convertNameToURL($name) . ".png")) {
		$headshot_path = "../Images/Players/Headshots/" . convertNameToURL($name) . ".png";
	}
	else {
		$headshot_path = "../Images/Players/Headshots/no_name.png";
	}


	$display_row = array();
	foreach ($row as $k=>$v) {
		$display_row[$k] = convert_to_info($k, $row[$k]);
		$display_row[$k] = convert_to_filter_link($k, $row[$k], $display_row[$k]);
	}

	echo "<div class='headshot_info'><div class='headshot_div'>";
	echo "<img class='headshot player_headshot' src='" . $headshot_path . "'><br/>";
	echo "</div>";

	echo "<div class='info_div'>";
	echo "<span class='info_line' id='top_line'>";
	echo $display_row['current_team'] . '<span class="category"> | </span>' . $display_row['position'] . $display_row['jersey_num'];
	echo "</span><br>";

	echo "<span class='info_line' id='second_line'>";
	echo "<span class='category'>Weight: </span> " . $display_row['weight'] . ' ' . "<span class='category'>Height: </span>" . $display_row['height'];
	echo "</span><br>"; 

	echo "<span class='info_line' id='third_line'>";
	echo "<span class='category'>Age: </span> " . $display_row['age'] . ' ' . "<span class='category'>Experience: </span>" . $display_row['experience'];
	echo "</span><br>"; 

	echo "<span class='info_line' id='fourth_line'>";
	echo "<span class='category'>College: </span> " . $display_row['college'];
	echo "</span><br>"; 

	echo "<span class='info_line' id='fifth_line'>";
	if ($display_row['draft_pos'] === "N/A" && $display_row['draft_year'] === "N/A") {
		echo "<span class='category'>Drafted: </span>N/A";
	}
	else {
		echo "<span class='category'>Drafted: </span>#" . $display_row['draft_pos'] . ", " . $display_row['draft_year'];
	}
	echo "</span><br>";

	echo "<span class='info_line' id='bottom_line'>";
	echo "<span class='category'>'17-'18 salary: </span> " . $display_row['salary'];
	echo "</span>";

	echo "</div></div></div>";

	echo '<div id="table" class="statButtonsTables">';
		echo '<a href="#table"><div class="statButtons">';
		echo '<button onclick="toggleTable(0)" class="ui-button ui-widget ui-corner-all team_color_button" id="RSRegButton">Basic Regular Season Stats</button>';
		echo '<button onclick="toggleTable(1)" class="ui-button ui-widget ui-corner-all team_color_button" id="RSAdvButton">Advanced Regular Season Stats</button>';
		echo '<button onclick="toggleTable(2)" class="ui-button ui-widget ui-corner-all team_color_button" id="PLRegButton">Basic Playoff Stats</button>';
		echo '<button onclick="toggleTable(3)" class="ui-button ui-widget ui-corner-all team_color_button" id="PLAdvButton">Advanced Playoff Stats</button>';
		echo '</div></a>';
		echo '<br/><br/>';

		$stats_stmt = $db->prepare('SELECT DISTINCT s.* FROM PLAYER as p
			INNER JOIN SEASON_STATS as s
		WHERE s.player_id = :id AND s.season_type="regular_season"
		ORDER BY s.season DESC');
		$stats_stmt->bindValue(':id', $row['player_id'], SQLITE3_INTEGER);
		$stats_result = $stats_stmt->execute();

		// Display the player's stats in a table if found
		if ($stats_result->fetchArray()) {
			$stats_result = $stats_stmt->execute();
			$table_id = 'rs_regular';
			$description = "<p class='table_header' id='rs_regular_d'>" . "Basic Regular Season Stats:" . "</p>";
			include 'make_regular_stat_table.php';
			$table_id = 'rs_advanced';
			$description = "<p class='table_header' id='rs_advanced_d'>" . "Advanced Season Stats:" . "</p>";
			include 'make_advanced_stat_table.php';
	}
	else {
		echo "<p id='rs_no_table' class='no_table'>Sorry, " . $row['name'] . " has no regular season stats to show.</p>";
	}

	$stats_stmt = $db->prepare('SELECT DISTINCT s.* FROM PLAYER as p
		INNER JOIN SEASON_STATS as s
		WHERE s.player_id = :id AND s.season_type="playoffs"
		ORDER BY s.season DESC');
		$stats_stmt->bindValue(':id', $row['player_id'], SQLITE3_INTEGER);
		$stats_result = $stats_stmt->execute();

		// Display the player's stats in a table if found
		if ($stats_result->fetchArray()) {
			$stats_result = $stats_stmt->execute();
			$table_id = 'playoffs_regular';
			$description = "<p class='table_header' id='playoffs_regular_d'>" . "Basic Playoff Stats:" . "</p>";
			include 'make_regular_stat_table.php';
			$table_id = 'playoffs_advanced';
			$description = "<p class='table_header' id='playoffs_advanced_d'>" . "Advanced Playoff Stats:" . "</p>";
			include 'make_advanced_stat_table.php';
	}
	else {
		echo "<p id='playoff_no_table' class='no_table'>Sorry, " . $row['name'] . " has no playoff stats to show.</p>";
	}

	echo "</div></div>";
	echo '
		<form method="post" class="hidden" id="hidden_filter" action="../Filter/filter.php">
		<input id="hidden_button" type="button" value="Filter" name="filter_button">
		</form>';
?>