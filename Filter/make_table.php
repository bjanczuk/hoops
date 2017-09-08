<?php
   	$num_results = count($results_rows);
    if ($num_results == 0) {
    	echo "<h2 class='num_matches'>Sorry, no players pass that filter...</h2>";
    	echo "<p class='again_link'><a id='try_again' href='./'>Try Again</a></p>";
    }
    else {
    	$ending = ($num_results == 1 ? '' : 's');
    	$filler = (count($season_stat_keys) == 0 ? " player" : " season");
    	echo "<h2 class='num_matches'>This filter matches " . $num_results . $filler . $ending . ".</h2>";

	  	echo "<div class='table_div'>
	  			<table border=1 class='tablesorter' id='filter_results'>
	  			<thead>
				<tr>
				<th>Player</th>"; // always display the player name

		// always display either the current team or season's team
		if (count($season_stat_keys) == 0) {
			echo "<th data-tip='Current team' class=data-tip-top>TEAM</th>";
		}
		else {
			echo "<th data-tip='Team in the given season' class=data-tip-top>TEAM</th>";
		}

		foreach ($info_headers as $k=>$v) { // finish creating the rest of the header row
			if (in_array($k, $all_keys)) {
				$tip = $info_tips[$k];
				$header = $info_headers[$k];
				echo "<th data-tip=\"" . $tip . "\" class=data-tip-top>" . $header . "</th>";
			}
		}
		echo "</tr></thead>
			<tbody>";

			foreach ($results_rows as $key=>$row) {
				echo "<tr>";
				
				$input = '<a href="../Search/results.php?q=' . convertStringToQuery($row['name']) . '">';
				$input = $input . $row['name'] . "</a>";
				echo table_data($input);

				if (count($season_stat_keys) == 0) {
					$input = '<a href="../Search/results.php?q=' . convertStringToQuery($row['current_team']) . '">';
					$input = $input . $team_maps[$row['current_team']] . "</a>";
					echo table_data($input);
				}
				else {
					if ($row['team'] == 'NJ') {
						$row['team'] = 'BKN';
					}
					if ($row['team'] == 'SEA') {
						$row['team'] = 'OKC';
					}

					$input = '<a href="../Search/results.php?q=' .
						convertStringToQuery(array_flip($team_maps)[$row['team']]) . '">';
					$input = $input . $row['team'] . "</a>";
					echo table_data($input);
				}

				foreach ($info_headers as $k=>$v) {
					if (in_array($k, $all_keys)) {
						if ($k === "season_type") {
							echo table_data(convert_season_type($row[$k]));
						}
						else if ($k === "salary") {
							echo table_data(convert_salary($row[$k]));
						}
		    			else {
		    				echo table_data($row[$k]);
		    			}
		    		}
		    	}
		    	echo "</tr>";
		    }

		echo "</tbody></table></div>";
		echo "<p class='again_link'><a id='filter_again' href='./'>Filter Again</a></p>";
	}
?>