<?php
	echo $description;
  	echo "<table border=1 class='tablesorter " . $team_class .  " player_stats' id='" . $table_id . "'>
  			<thead>
			<tr>
			<th data-tip='Season played' class=data-tip-top>Season</th>
			<th data-tip='Team for which the most games were played' class=data-tip-top>Team   </th>
			<th data-tip='Years of experience before that season' class=data-tip-top>EXP</th>
			<th data-tip='Total games in the season in which this player played' class=data-tip-top>GP   </th>
			<th data-tip='Total games in the season in which this player started' class=data-tip-top>GS   </th>
			<th data-tip='Minutes played per game' class=data-tip-top>MIN</th>

			<th data-tip='Points scored per game' class='spaced' class=data-tip-top>PTS</th>
			<th data-tip='Field goals made per game' class=data-tip-top>FGM</th>
			<th data-tip='Field goals attempted per game' class=data-tip-top>FGA</th>
			<th data-tip='Field goal percentage' class=data-tip-top>FG%</th>
			<th data-tip='Three pointers made per game' class=data-tip-top>3PM</th>
			<th data-tip='Three pointers attempted per game' class=data-tip-top>3PA</th>
			<th data-tip='Three point field goal percentage' class=data-tip-top>3P%</th>
			<th data-tip='Free throws made per game' class=data-tip-top>FTM</th>
			<th data-tip='Free throws attempted per game' class=data-tip-top>FTA</th>
			<th data-tip='Free throw percentage' class=data-tip-top>FT%</th>

			<th data-tip='Offensive rebounds per game' class='spaced' class=data-tip-top>OREB</th>
			<th data-tip='Defensive rebounds per game' class=data-tip-top>DREB</th>
			<th data-tip='Total rebounds per game' class=data-tip-top>REB</th>
			<th data-tip='Assists made per game' class=data-tip-top>AST</th>
			<th data-tip='Steals made per game' class=data-tip-top>STL</th>
			<th data-tip='Blocks made per game' class=data-tip-top>BLK</th>
			<th data-tip='Turnovers committed per game' class=data-tip-top>TOV</th>
			<th data-tip='Fouls committed per game' class=data-tip-top>FLS</th>
		</tr></thead>
		<tbody>
		<tr>";

	while ($stats_row = $stats_result->fetchArray()) {
	    echo table_data($stats_row['season']) .
	    	 table_data($stats_row['team']) .
	    	 table_data($stats_row['year_exp']) .
	    	 table_data($stats_row['games_played']) .
	    	 table_data($stats_row['games_started']) .
	    	 table_data($stats_row['minutes']) .
	    	 table_data($stats_row['points']) .
	    	 table_data($stats_row['fgm']) .
	    	 table_data($stats_row['fga']) .
	    	 table_data($stats_row['fgp']) .
	    	 table_data($stats_row['tpm']) .
	    	 table_data($stats_row['tpa']) .
	    	 table_data($stats_row['tpp']) .
	    	 table_data($stats_row['ftm']) .
	    	 table_data($stats_row['fta']) .
	    	 table_data($stats_row['ftp']) .
	    	 table_data($stats_row['o_rebounds']) .
	    	 table_data($stats_row['d_rebounds']) .
	    	 table_data($stats_row['rebounds']) .
	    	 table_data($stats_row['assists']) .
	    	 table_data($stats_row['steals']) .
	    	 table_data($stats_row['blocks']) .
	    	 table_data($stats_row['turnovers']) .
	    	 table_data($stats_row['fouls']) . "</tr><tr>";
	}

	echo "</tr></tbody></table>";
?>