<?php
	while ($row = $result->fetchArray()) {
		echo '<div id="' . convertNameToURL($row['team_name']) . '_div">';
		echo '<div id="team_header_background" class="' . convertNameToURL($row['team_name']) . ' team_background_top">';

		$name = $row['team_name'];
		$arena = $row['arena_name'];
		$coach = $row['coach'];
		$gotMatch = true;
		
		foreach (glob(('../Images/Teams/Logos/' . globString($name) . '.png')) as $filename) {
			if (file_exists($filename)) {
			    $logo_path = $filename;
			    break;
			}
		}
		echo "<div class='logo'>";
		echo "<img id='logo_img' src='" . $logo_path . "'>";
		echo '</div></div>';
		echo "<br/><br/>";

		echo '';

		// Display each player's name, position, and headshot for the roster carousel
		echo '<div class="team_info">
				<br/><br/><h1 id="roster_label" class="text_team_color">Roster:</h1><br/>
				<div class="team_roster">';

		// Add the coach to the carousel before any players
		echo '<a href="results.php?q=' . convertStringToQuery($name) . '">';
		echo '<div class="player_in_carousel" >';
			if (file_exists("../Images/Coaches/" . convertNameToURL($coach . ".jpg"))) {
				echo '<img class="headshot headshot_in_carousel" src="../Images/Coaches/' . convertNameToURL($coach . ".jpg") . '">';
			}
			else {
				echo '<img class="headshot headshot_in_carousel no_name_headshot" src="../Images/Players/Headshots/no_name.png">';
			}
			echo '<p class="player_name">' . $coach . ' (Coach)</p>';
		echo '</div></a>';

		foreach ($row as $key => $val) {
			if (strpos($key, "player") === 0) {
				if ($row[$key] != NULL) {
					// Get the player's position first
					$position_stmt = $db->prepare('SELECT position FROM PLAYER
						WHERE name=:p_name');
					$position_stmt->bindValue(':p_name', $row[$key], SQLITE3_TEXT);
				  	$position_result = $position_stmt->execute();
				  	$position = $position_result->fetchArray()['position'];

					echo '<a href="results.php?q=' . convertStringToQuery($row[$key]) . '">';
					echo '<div class="player_in_carousel" >';

					if (file_exists("../Images/Players/Headshots/" .
						convertNameToURL($row[$key]) . ".png")) {
						echo '<img class="headshot headshot_in_carousel"
							src="../Images/Players/Headshots/' .
							convertNameToURL($row[$key]) . '.png">';
					}
					else {
						echo '<img class="headshot headshot_in_carousel no_name_headshot"
							src="../Images/Players/Headshots/no_name.png">';
					}
					echo '<p class="player_name">' . $row[$key] . ' (' . $position . ')</p>';
					echo '</div></a>';
				}
			}
		}
		echo '</div>';

		$arena_path = '../Images/Teams/Arenas/' . convertNameToURL($arena) . '.jpg';
		echo "<br/><h1 class='arena_header'>Arena: <span id='arena_name_text' class='text_team_color'>" . $arena . '</span></h1>';
		echo "<img class='arena' src='" . $arena_path . "'>";
		echo '<br/><p class="arena_description">' . $row['arena_description'] . '</p><br/>';

		echo '</div></div>';
	}
?>